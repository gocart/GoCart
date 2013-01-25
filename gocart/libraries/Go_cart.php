<?php  


/* 
*  Gocart Cart Library
*  Based on cart.php included with Codeigniter
*/

/*  
*	Coupon Support
*    This cart accepts coupons, Two main types:
		- Whole order discount 
		- Individual product discounts
	 Discount Types:
	 	- percent of price
		- fixed discount
	 Usage Restrictions (optional):
	 	- Number of uses
		- Product instance limit (ex. applying to up to x number of products per use)
		- Date range, expiration
		- Only one coupon can be applied to a prodct. No Doubling, etc.
	Coupon Discount Logic:
		- Validate usage limitation and expiration
      	- Apply only one coupon to each individual item (up to the instance limit for each item)
		- Adhere to coupon instance restrictions by applying  discount to x number of items
		- Maximize the savings for the customer by applying the best discount possible
*/

class go_cart {
	var $CI;
	

	// Cart properties and items will go in here
	//  Modified from the original cart lib as follows:
	//    _cart_contents[ (cart property indexes) ] = (property value)
	//    _cart_contents[items]	= (shopping cart products list)
	// This has to be in a single variable for easy session storage
	var $_cart_contents	= array();
	
	var $gift_cards_enabled = false;
	
	function __construct() 
	{
		$this->CI =& get_instance();
		$this->CI->load->model(array('Coupon_model' , 'Gift_card_model', 'Settings_model', 'Digital_Product_model'));
		
		// Load the saved session
		if ($this->CI->session->userdata('cart_contents') !== FALSE)
		{
			$this->_cart_contents = $this->CI->session->userdata('cart_contents');
		}
		else
		{
			// Or init a new session
			$this->_init_properties();
		}
		
		$gc_setting = $this->CI->Settings_model->get_settings('gift_cards');
		if(@$gc_setting['enabled']==1)
		{
			$this->gift_cards_enabled = true;
		}
		
		//die(var_dump($this->_cart_contents));
	}

	private function _init_properties($totals_only=false, $preserve_customer=false)
	{
		
		// shipping data
		$this->_cart_contents['order_insurable_value']		= 0;
		$this->_cart_contents['order_weight']				= 0;
		// This is the discount amount to subtract from the cart total when the order is finalized
		$this->_cart_contents['group_discount']				= 0;
		$this->_cart_contents['coupon_discount'] 			= 0;
		$this->_cart_contents['taxable_coupon_discount']	= 0;
		$this->_cart_contents['gift_card_balance'] 			= 0;
		$this->_cart_contents['gift_card_discount'] 		= 0;
		$this->_cart_contents['downloads']					= array();
		// totals
		$this->_cart_contents['cart_subtotal']		 		= 0;
		$this->_cart_contents['cp_discounted_subtotal'] 	= 0;
		$this->_cart_contents['taxable_total'] 				= 0; // omits the price of digital products
		$this->_cart_contents['cart_total'] 				= 0;
		$this->_cart_contents['total_items'] 				= 0;
		$this->_cart_contents['shipping_total'] 			= 0;
		// tax
		$this->_cart_contents['tax'] 						= 0;
		
		// We want to preserve the cart items and properties, but reset total values when recalculating
		if( ! $totals_only) 
		{
		
			// product items will live in here
			$this->_cart_contents['items'] = array();
			
			if(!$preserve_customer)
			{
				// customer data container
				$this->_cart_contents['customer'] = false;
			}
			
			// custom charges
			$this->_cart_contents['custom_charges']				= array();
			
			// shipping details container
			$this->_cart_contents['shipping']['method']	= false;  // defaults
			$this->_cart_contents['shipping']['price']	= false;
			$this->_cart_contents['shipping']['code']	= false;
			
			// This is the list of gift cards that are attached to the cart
			//   to be applied toward a price reduction
			//   (not to be confused with a gift card purchase)
			$this->_cart_contents['gc_list'] = array();
			
			// This is the "pool" of coupons that the customer has entered
			//  - products added after a coupon is attached to the cart will be factored into the discount logic
			//  - not all coupons in the pool will be applied to a discount for the customer
			//  - those that are not applied are ignored
			$this->_cart_contents['coupon_list'] = array();
			
			// This is the list of coupons will be applied toward a price discount
			// we need to keep track of these so their usage can be updated once the order is confirmed to conform with usage limitations
			$this->_cart_contents['applied_coupons'] = array();
			
			// Container for possible whole order level discount formulas
			//  whole order coupons will be tracked and counted by indexing this array with the coupon code
			$this->_cart_contents['whole_order_discounts'] = array();
			
			// If free shipping coupons are configured, we have to track and count which one is used
			$this->_cart_contents['free_shipping_coupon'] = false;
			
			// This is a flag to determine if we need to charge for shipping
			//  Stays false if all products are non-shippable (downloads)
			$this->_cart_contents['requires_shipping'] = false;
			
			// Container for payment details
			$this->_cart_contents['payment'] = array();
			
		}
	}
	
	/*******************************************************
	*
	* Private Methods
	*
	********************************************************/
	
	
	private function _insert($item)
	{
		//on update clear the payments & shipping
		$this->clear_payment();
		$this->clear_shipping();
		
		// Was any cart data passed? No? Bah...
		if ( ! is_array($item) OR count($item) == 0)
		{
			return FALSE;
		}
		
		$cartkey = false;
		//is there an existing key?
		if(!empty($item['cartkey']))
		{
			$cartkey	= $item['cartkey'];
		}
		// now remove it whether or not it was empty
		unset($item['cartkey']);
		
		
		//record the quantity
		$quantity	= ($item['fixed_quantity']==0) ? $item['quantity'] : 1;
		
		//remove quantity from the row ID hash this will enable us to add
		//the same item twice without having it appear twice due to quantity differences
		unset($item['quantity']);
		
		// Generate our row ID by the entire item array
		$newkey = md5(serialize($item));	
		
		//add quantity back in and we ceil it just in case someone is being silly submitting a decimal
		$item['quantity']	= ceil($quantity);
		
		//check to see if the item already exists in the cart
		//if it does, add the new quantity to the existing quantity
		//if it does not, add it as a new item
		
		//gotta do this differently to make sure the items stay in the same order
		if($cartkey)
		{
			$new_list	= array();
			foreach($this->_cart_contents['items'] as $key=>$i)
			{
				if($key == $cartkey)
				{
					//remove the old cart key (this is needed in order to fix coupons)
					$this->_remove($key);
					
					// replace the old item with the new item
					$new_list[$newkey] = $item;
				}
				else
				{
					$new_list[$key]	= $i;	
				}
			}
			$this->_cart_contents['items'] = $new_list;
		}
		else
		{
			//this is for non-edited products (except for quantity)
			if(isset($this->_cart_contents['items'][$newkey]))
			{
				//make sure that fixed quantity items remain fixed quantity
				if(!(bool)$item['fixed_quantity'])
				{
					$this->_cart_contents['items'][$newkey]['quantity'] = $this->_cart_contents['items'][$newkey]['quantity'] + $item['quantity'];
				}
			}
			else
			{
				// add our item to the items list
				$this->_cart_contents['items'][$newkey] = $item;
			}
		}
	
		// Run the product through the coupons list to check if there is a coupon which applies to it
		// cart contents item details and coupon data are automatically updated
		$this->_check_product_for_discount($newkey);

		// Woot!
		return TRUE;
	}
	
	private function _remove($cartkey) 
	{
		
		if(!isset($this->_cart_contents['items'][$cartkey])) return false;
		
		// kill coupon association
		if(isset($this->_cart_contents['items'][$cartkey]['coupon_code']))
		{
			unset($this->_cart_contents['applied_coupons'][$this->_cart_contents['items'][$cartkey]['coupon_code']][$cartkey]);
			// if there are no other discount lists for this coupon, remove it from the applied list altogether
			if(empty($this->_cart_contents['applied_coupons'][$this->_cart_contents['items'][$cartkey]['coupon_code']])) 
			{
				unset($this->_cart_contents['applied_coupons'][$this->_cart_contents['items'][$cartkey]['coupon_code']]);
			}
		}
		
		// Remove the item from our items list
		unset($this->_cart_contents['items'][$cartkey]);
		
		return true;
	}
	
	private function _update($cartkey, $quantity)
	{
		//on update clear the payments & shipping
		$this->clear_payment();
		$this->clear_shipping();
		
		if(!isset($this->_cart_contents['items'][$cartkey]))
		{
			return false;
		}
		
		// update cart, fixed quantity items restricted to 1
		if($this->_cart_contents['items'][$cartkey]['fixed_quantity']==0)
		{
			$this->_cart_contents['items'][$cartkey]['quantity'] = ceil($quantity);
		} else {
			$this->_cart_contents['items'][$cartkey]['quantity'] = 1;
		}
		
		// Update associated coupon discount data
		if(isset($this->_cart_contents['items'][$cartkey]['coupon_code']))
		{
			// see _apply_coupon_to_product method for explanation of what this array list is for
			$this->_cart_contents['applied_coupons'][$this->_cart_contents['items'][$cartkey]['coupon_code']][$cartkey] = array();
			for($x=0;$x<$quantity;$x++) 
			{
				$this->_cart_contents['applied_coupons'][$this->_cart_contents['items'][$cartkey]['coupon_code']][$cartkey][] = $this->_cart_contents['items'][$cartkey]['coupon_discount'];
			}
		}
		
		return true;
	}
	
	
	// This method is for applying existing coupons to NEW products
	private function _check_product_for_discount($cartkey) {
		
		// Loop through our coupon pool to see if any apply to the newly added product
		foreach($this->_cart_contents['coupon_list'] as $code=>$contents)
		{
			// does the current coupon apply to the product we are adding?
			if(in_array($this->_cart_contents['items'][$cartkey]['id'], $contents['product_list']))
			{
								
				// try to apply the coupon
				$this->_apply_coupon_to_product($cartkey, $code);
			}
		
		}
	
	}
	
	// Applies a coupon to a product and calculates the discount amount
	private function _apply_coupon_to_product($cartkey, $coupon_code) 
	{
		
		// calculate discount amount
		$price = (float) $this->_cart_contents['items'][$cartkey]['price'];
		
		if($this->_cart_contents['coupon_list'][$coupon_code]['reduction_type']=="percent")
		{
			//make sure we're removing the right percentage
//			$reduction_amount	= 100 - $coupon['reduction_amount'];
//			$str = ' - ($subtotal * ('. $reduction_ammount .' /100))';
			
			
			$reduction_amount	= 100 - (float) $this->_cart_contents['coupon_list'][$coupon_code]['reduction_amount'];
			$discount			= ($price * ($reduction_amount/100));
			$discount_amount	= abs($price-$discount);
		} 
		else
		{
			$discount_amount = (float) $this->_cart_contents['coupon_list'][$coupon_code]['reduction_amount']; 
			// Prevent fixed discounts from resulting a negative discount amount
			if($discount_amount > $this->_cart_contents['items'][$cartkey]['price'])
			{
				$discount_amount = $this->_cart_contents['items'][$cartkey]['price'];
			}
		}
		
		// Check for existing discount
		if(isset($this->_cart_contents['items'][$cartkey]['coupon_discount'] ))
		{
			// is the new discount a better deal? otherwise leave it as it is
			if($this->_cart_contents['items'][$cartkey]['coupon_discount'] < $discount_amount)
			{
				
				$old_code = $this->_cart_contents['items'][$cartkey]['coupon_code'];
				// replace previous value
				$this->_cart_contents['items'][$cartkey]['coupon_discount'] = $discount_amount;
				$this->_cart_contents['items'][$cartkey]['coupon_code'] = $coupon_code;
				
				// Un-apply the previously applied coupon by removing the old code placeholder from the cart item array
				unset($this->_cart_contents['applied_coupons'][$old_code][$cartkey]);
				// if there are no other discount lists for this coupon, remove it from the applied list altogether
				if(empty($this->_cart_contents['applied_coupons'][$old_code])) 
				{
					unset($this->_cart_contents['applied_coupons'][$old_code]);
				}
				
				return true;

			} 
		} else {
			// If no existing discount, just set it
			$this->_cart_contents['items'][$cartkey]['coupon_discount'] = $discount_amount;
			$this->_cart_contents['items'][$cartkey]['coupon_code'] = $coupon_code;
				
			
			  // Construct a list containing product discount amounts resulting from all applied coupons
			  // We have to index this by the cartkey, so it can be updated when the quantity is changed, sorted to siphon the biggest discounts,
			  //    conforming to instance limitation by only adding x number of item discounts to the final discount total
			  //    To start, it needs to look like:
			  //     applied_coupons[ coupon code index ][ product key index] = array list of discounts,repeated to product quantity
			
			for($x=0;$x<$this->_cart_contents['items'][$cartkey]['quantity'];$x++) 
			{
				$this->_cart_contents['applied_coupons'][$coupon_code][$cartkey][] = $discount_amount;
			}
			
			return true;
			
		}
		
		return false;
	}
	
	
	private function _insert_coupon($coupon_code)
	{
		
		if(!$coupon_code) return false;
		
		$coupon = $this->CI->Coupon_model->get_coupon_by_code($coupon_code);

		// Is the code valid?
		if($coupon) 
		{
			// Make sure they can't submit the same coupon more than once
			if(!isset($this->_cart_contents['coupon_list'][$coupon_code]))
			{
				
				$is_applied = false;
				
				// check validity
				if($this->CI->Coupon_model->is_valid($coupon)) {
					
					// add code to the coupon pool
					$this->_cart_contents['coupon_list'][$coupon_code] = $coupon;
					
					// apply coupon discount for free shipping, whole order discount, or product level
					if ($coupon['reduction_target'] =="shipping") {
						// Remember what code was used for free shipping
						$this->_cart_contents['free_shipping_coupon'] = $coupon_code;
						
						$is_applied = true;
						
						// is the coupon a whole-order discount coupon?
					} else if($coupon['whole_order_coupon']=="1")
					{
						// save a discount formula to be evaluated later
						//  when determining the total order discount
						if($coupon['reduction_type'] == "fixed")
						{
							$str = $coupon['reduction_amount'];
						} else {
							//we need to swap percentages
							//ex 20% discount needs to return an 80% price
							$reduction_ammount	= 100 - $coupon['reduction_amount'];
							$str = ' - ($subtotal * ('. $reduction_ammount .' /100))';
						}
						
						$is_applied = true;
						
						// We want to keep an array, in case they have more than a single whole-order discount coupon
						//  but we will only apply the one which yields the best discount
						$this->_cart_contents['whole_order_discounts'][$coupon_code] = $str;
						
					} 
					
					else  // Otherwise, this is a product level discount
					{ 
						
						
						// loop through our items and see if any of them can be discounted by this coupon
						foreach($this->_cart_contents['items'] as $key=>$item) {
						
							if(in_array($item['id'], $coupon['product_list'])) {
								// try to apply this coupon to the product
								if($this->_apply_coupon_to_product($key, $coupon['code'])) $is_applied = true;
							}
						}
						
					}		
					
					if(!$is_applied) {
						// message coupon added but not applied
						return array('message'=>lang('coupon_not_apply'));
					} else {
						// message coupon applied
						return array('message'=>lang('coupon_applied'));
					}
					
				} else {
					// message coupon no longer valid
					return array('error'=>lang('coupon_invalid'));
				}
				
			} else {
				// message coupon already applied
				return array('error'=>lang('coupon_already_applied'));
			}
		} else {
			// invalid code error message
			return array('error'=>lang('invalid_coupon_code'));
		}	
	}
	
	
	// Calculate the best possible discount within the product instance limitations for the whole cart
	// return the discount amount
	private function _calculate_coupon_discount()
	{
		$total_discount = 0;
		//keep tabs on how much is taxable
		$taxable_discount = 0;
		
		// Get the sum of the product-level coupons
		if( ! empty($this->_cart_contents['applied_coupons']))
		{
			foreach($this->_cart_contents['applied_coupons'] as $code=>$discount_list)
			{
				// The discount list is an array of arrays, indexed by cart key
				//  we need to prep this list for a final discount aggregation
				//  by collapsing this into a singular array of all product discounts
				//  from which we will calculate the total discount per coupon
				$collapsed		= array();
				$product_index	= array();
				$x = 0; // we will use this to cross-index what discounts belong to what product
						// so that we can separate taxable from non-taxable amounts
				foreach($discount_list as $key=>$item)
				{
					foreach($item as $discount)
					{
						$collapsed[$x]		= $discount;
						$product_index[$x]	= $key;
						$x++;
					}
					// because each product can only have one coupon associated
					//  we can prep a total discount here for each item for later dealing with the amounts individually
					$this->_cart_contents['items'][$key]['total_coupon_discount'] = 0;
				}
							
				// sort the list, highest discount amounts on top
				rsort($collapsed);
				
				// either no limit or the limit is greater than the size of the list
				if($this->_cart_contents['coupon_list'][$code]['max_product_instances'] == 0 || count($collapsed)<=$this->_cart_contents['coupon_list'][$code]['max_product_instances'])
				{
					$maximum = count($collapsed); 
				}
				else
				{
					$maximum = $this->_cart_contents['coupon_list'][$code]['max_product_instances'];
				}
				
				// Only calculate to the limit of instances for this coupon
				for($x=0;$x<$maximum;$x++)
				{
					$total_discount += $collapsed[$x];
					// store the total discount in the item details, for future reference
					$this->_cart_contents['items'][$product_index[$x]]['total_coupon_discount'] += $collapsed[$x];
					// taxable?
					if( $this->_cart_contents['items'][$product_index[$x]]['taxable'] == 1 )
					{
						$taxable_discount +=  $collapsed[$x];
					}
				}
			}
		}
		
		// Calculate whole order discount
		//  If a customer has more than one whole order coupon and enters them,
		//  we only want to use the one that results in the best discount (no doubling, etc)
		
		if(!empty($this->_cart_contents['whole_order_discounts']))
		{
			$subtotal = $this->_cart_contents['cart_subtotal'];
			$temp = 0;
			foreach($this->_cart_contents['whole_order_discounts'] as $code=>$disc)
			{
				if(is_numeric($disc))
				{
					$discount_amount = $disc;
				}
				else
				{
					eval('$discount_amount=$subtotal'.$disc.';');
				}
				
				if($discount_amount > $temp)
				{
					$temp = $discount_amount;
					$this->_cart_contents['whole_order_discount_cp'] = $code; // track which code we use
				}
			}
			// coupon discounts and whole order discounts can be cumulated
			$total_discount += $temp;
			$total_whole_order_discount = $temp;

			// iterate products and apply calculated whole order discount % to taxable ones
			if ($this->_cart_contents['cart_subtotal'] > 0){
				$whole_order_discount_ratio = $total_whole_order_discount / (float)$this->_cart_contents['cart_subtotal'];
				foreach ($this->_cart_contents['items'] as $product)
				{
					if ($product['taxable'] == 1)
					{
						$taxable_discount += $whole_order_discount_ratio * (float)$product['price'] * (int)$product['quantity'];
					}
				}
			}
		}
		
		$this->_cart_contents['cp_discounted_subtotal'] = $this->_cart_contents['cart_subtotal'] - $total_discount;
		$this->_cart_contents['coupon_discount'] = $total_discount;
		// this is the portion of the discount that applies to a taxable amount
		$this->_cart_contents['taxable_coupon_discount'] = $taxable_discount;
	}
	
	
	
	
	// Attach a Gift Card discount to the order
	private function _attach_gift_card($gc_code)
	{
		//on when attaching a gitcard reset payments and shipping
		$this->clear_payment();
		$this->clear_shipping();
		
		// enabled?
		if( ! $this->gift_cards_enabled) return;
		
		if ($gc_code) 
		{	
			if ( ! isset($this->_cart_contents['gc_list'][$gc_code]) )
			{
				$gift_card = $this->CI->Gift_card_model->get_gift_card($gc_code);
				
				if($gift_card)
				{	
					
					// valid code?
					if($this->CI->Gift_card_model->is_valid($gift_card))
					{
						// Add to the cart list
						$this->_cart_contents['gc_list'][$gc_code]['balance'] = $this->CI->Gift_card_model->get_balance($gift_card);
						
						// update balance of all gift cards attached
						$this->_cart_contents['gift_card_balance'] += $this->_cart_contents['gc_list'][$gc_code]['balance'];
						
						// message coupon applied
						return array('message'=>lang('giftcard_balance_displayed'));
					} else {
						// invalid card (expired or zero balance)
						return array('error'=>lang('giftcard_zero_balance'));
					}
				} else {
					 // invalid card code
					 return array('error'=>lang('giftcard_not_exist'));
				}
			} else {
				// already applied
				return array('message'=>lang('giftcard_already_applied'));
			}
		}
	}
	

	private function _calculate_gift_card_discount()
	{
		// no cards are set in the cart
		if(empty($this->_cart_contents['gc_list'])) return;
		
		
		// calculate what will be taken from the card(s)
		foreach($this->_cart_contents['gc_list'] as &$card)
		{
			// If the card balance has more than enough to cover the total

			if($card['balance'] >= $this->_cart_contents['cart_total']) // cart total should be calculated already 
			{
				// we don't want to change the card balance in the cart yet, so we just keep track of how much is taken from each one
				$card['amt_used'] = $this->_cart_contents['cart_total']; //for later tracking
				$this->_cart_contents['gift_card_discount'] = $this->_cart_contents['cart_total'];
				$this->_cart_contents['cart_total'] = 0;
				$this->_cart_contents['gift_card_balance']  += $card['balance'] - $card['amt_used']; // adds to a cart total balance (for all gc)
				
				// set payment placeholders, we will be skipping that in the checkout
				$this->_cart_contents['payment']['method']		= "Gift Card";
				$this->_cart_contents['payment']['description'] = "Paid by Gift Card";
				
				return; // complete discount acheived (total=0), stop procesing
			
			// Otherwise, discount up to the card balance
			} else {
				$card['amt_used'] = $card['balance'];
				$this->_cart_contents['gift_card_discount'] += $card['balance'];
				$this->_cart_contents['cart_total'] 		-= $card['balance'];
			}	
		}
	}

	/**
	 * Get Item
	 *
	 * Returns a specific item if it's there, otherwise returns false
	 *
	 * @access	public
	 * @return	array
	 */
	function item($key)
	{
		if(!empty($this->_cart_contents['items'][$key]))
		{
			return $this->_cart_contents['items'][$key];
		}
		else		
		{
			return false;
		}
	}
	
	/**
	 * Save the cart array to the session DB
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _save_cart($recalculate=true)
	{
		
		// Once in the check-out stage, we no longer need to keep recalculating totals
		//  taxes and shipping will be added in later
		if($recalculate) 
		{
			// Reset totals
			$this->_init_properties(true);
			
			// Lets add up the individual prices and set the cart sub-total
			$total 			= 0;
			$taxable 		= 0;
			$coupon_total 	= 0;
			$this->_cart_contents['requires_shipping'] = false; // Go back to default and redetermine if there is anything shippable
			
			foreach ($this->_cart_contents['items'] as $key => &$val)
			{
				// Apply any group discount
				if(isset($this->_cart_contents['customer']['group_discount_formula']))
				{
					// calculate the discount amount
					eval('$this_price=$val["price"]'. $this->_cart_contents['customer']['group_discount_formula'] .';');
					
					// add to the total group discount
					$this->_cart_contents['group_discount'] 	+=  ($val['price'] - $this_price) * $val['quantity'];
				} else {
					// or use the regular price
					$this_price = $val['price'];
				}
				
				// Deal with shippable (if shipping is disabled in the config then go with that!)
				if ( $val['shippable']== 1 )
				{
					// shipping insurable value & weight
					$this->_cart_contents['order_insurable_value']  += $this_price;
					$this->_cart_contents['order_weight'] 			+= $val['weight']*$val['quantity'];
					$this->_cart_contents['requires_shipping'] 		= true;
				}
				
				// charge tax?
				if($val['taxable'] == 1)
				{
					$taxable 		+= ($this_price * $val['quantity']);
				}
				
				$total 			+= ($this_price * $val['quantity']);
				
				// set product subtotal (NOT accounting for coupon discount yet)
				$val['subtotal'] = ($this_price * $val['quantity']);
			
			}
			
			// total products in the cart
			$this->_cart_contents['total_items'] = count($this->_cart_contents['items']);	
			
			// Set the cart price totals ...
			$this->_cart_contents['cart_subtotal'] = $total;
			
			// Calculate / set total coupon discount amounts
			$this->_calculate_coupon_discount();
			
			// set taxable subtotal
			$this->_cart_contents['taxable_total'] = $taxable - $this->_cart_contents['taxable_coupon_discount'];
			
			$this->_cart_contents['cart_total'] = $total - $this->_cart_contents['coupon_discount']; // $this->_cart_contents['group_discount'];
			
			
			// add any additional custom charges
			if(!empty($this->_cart_contents['custom_charges']))
			{
				foreach($this->_cart_contents['custom_charges'] as $c)
				{
					$this->_cart_contents['cart_total'] += $c;
				}
			}
			
			
			// Compute taxes BEFORE shipping costs are added in?
			if(! $this->CI->config->item('tax_shipping')) 
			{
				$this->_compute_tax();
			}
			
			// Shipping costs
			if($this->_cart_contents['requires_shipping']) 
			{
				$this->_cart_contents['cart_total']		+= $this->_cart_contents['shipping']['price'];
				$this->_cart_contents['taxable_total']	+= $this->_cart_contents['shipping']['price'];
			}
			else
			{
				// placeholders
				$this->_cart_contents['shipping']['method']	= false;  // defaults
				$this->_cart_contents['shipping']['price']	= false;
			}
			
			// Compute taxes AFTER shipping costs are added in ?
			if($this->CI->config->item('tax_shipping')) 
			{
				$this->_compute_tax();
			}
			
			// finally
			$this->_cart_contents['cart_total'] += $this->_cart_contents['tax'];

			// set any gift card reduction
			// updates totals accordingly
			if($this->gift_cards_enabled) 
			{
				$this->_calculate_gift_card_discount();
			}
		}		
		
		// Save up
		$this->CI->session->set_userdata(array('cart_contents' => $this->_cart_contents));

		// Woot!
		return TRUE;	
	}
	
	private function _compute_tax()
	{
		$this->CI->load->model('Tax_model');
		$this->_cart_contents['tax'] =  $this->CI->Tax_model->get_tax_total();
	}
	
	
	/*******************************************************
	*
	* Public Methods
	*
	********************************************************/
	
	/**
	 * Double check that each item has enough in stock from the database
	 *
	 * @access	public
	 * @return	bool
	 */
	function check_inventory()
	{
		$contents	= $this->contents();
		
		//this array merges any products that share the same product id
		$new_contents	= array();
		foreach($contents as $c)
		{
			// skip gift card products
			if($c['is_gc']) 
			{
				continue;
			}
			
			//combine any product id's and tabulate their quantities
			if(array_key_exists($c['id'], $new_contents))
			{
				$new_contents[$c['id']]	= intval($new_contents[$c['id']])+intval($c['quantity']);
			}
			else
			{
				$new_contents[$c['id']]	= $c['quantity'];
			}
		}
		
		$error	= '';
		$this->CI->load->model('Product_model');
		foreach($new_contents as $product_id => $quantity)
		{
			$product	= $this->CI->Product_model->get_product($product_id);
			
			//make sure we're tracking stock for this product
			if((bool)$product->track_stock)
			{
				if(intval($quantity) > intval($product->quantity))
				{
					$error .= '<p>'.sprintf(lang('not_enough_stock'), $product->name, $product->quantity).'</p>';
				}
			}
		}
		
		if(!empty($error))
		{
			return $error;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Insert items into the cart and save it to the session table
	 *
	 * @access	public
	 * @param	array
	 * @return	bool
	 */
	function insert($items = array())
	{
		// Was any cart data passed? No? Bah...
		if ( ! is_array($items) OR count($items) == 0)
		{
			return FALSE;
		}
		
		//reset payment options so they get reset if someone adds a product at a later time
		
		// You can either insert a single product using a one-dimensional array, 
		// or multiple products using a multi-dimensional one. The way we
		// determine the array type is by looking for a required array key named "id"
		// at the top level. If it's not found, we will assume it's a multi-dimensional array.
	
		$save_cart = FALSE;		
		if (isset($items['id']))
		{			
			if ($this->_insert($items) == TRUE)
			{
				$save_cart = TRUE;
			}
		}
		else
		{
			foreach ($items as $val)
			{
				if (is_array($val) AND isset($val['id']))
				{
					if ($this->_insert($val) == TRUE)
					{
						$save_cart = TRUE;
					}
				}			
			}
		}

		// Save the cart data if the insert was successful
		if ($save_cart == TRUE)
		{
			$this->_save_cart();
			
			//clear the shipping after the cart is updated.
			//This will ensure the rates need to be reset if they would differ
			$this->clear_shipping();
			
			return TRUE;
		}

		return FALSE;
	}
	


	function update_cart($qty_list=false, $coupon_code=false, $gc_code=false)
	{
		// We might not need to save the cart, if nothing changes
		$save_cart = false;
		$response = false;
		
		$error = '';
		$message = '';
		
		//on update clear the payments & shipping
		$this->clear_payment();
		$this->clear_shipping();
		
		// insert any coupons that might be sent
		if($coupon_code) 
		{
			$cp_status = $this->_insert_coupon($coupon_code);
			if(isset($cp_status['message'])) $save_cart = true;
		
			// collect message
			if(isset($cp_status['error'])) $error .= '<p>'. $cp_status['error'].'</p>';
			if(isset($cp_status['message'])) $message  .= '<p>'.$cp_status['message'].'</p>';
			$response = true;
		}
		
		
		// attach any gift cards
		if($gc_code)
		{
			$gc_status = $this->_attach_gift_card($gc_code);
			if(isset($gc_status['message'])) $save_cart = true;
			
			// collect message
			if(isset($gc_status['error']))  $error	.= '<p>'. $gc_status['error'].'</p>';
			if(isset($gc_status['message'])) $message .= '<p>'.$gc_status['message'].'</p>';
			
			$response = true;
		}
		
		// this message stuff could be cleaned up
		if($response)
		{
			$response = array();
			$response['error'] = $error;
			$response['message'] = $message;
		}
		
		if($qty_list)
		{
			foreach($qty_list as $item_key=>$qty)
			{
				if(!is_numeric($qty)) continue; // ignore if there's garble in it
				
				//have to run an isset here due to an error in IE (presumeably due to self signed certificate error double loading)
				if(isset($this->_cart_contents['items'][$item_key]) && $this->_cart_contents['items'][$item_key]['quantity'] != $qty)
				{	
					if((int)$qty>0)
					{
						// don't update the quantity of no_quantity items 
						if(!isset($this->_cart_contents['items'][$item_key]['no_quantity'])) $this->_update($item_key, $qty);
					} else {
						$this->_remove($item_key);
					}
					$save_cart = true;
				}

			}
		}
		
		if($save_cart)
		{
			$this->_save_cart();
		}

		return $response;
	}
	
	// save / get order download list
	function save_order_downloads($list)
	{
		$this->_cart_contents['downloads'] = $list;
	}
	
	function get_order_downloads()
	{
		return $this->_cart_contents['downloads'];
	}
	
	//save additional setting
	function set_additional_detail($key, $data)
	{
		$this->_cart_contents[$key]	= $data;
		$this->_save_cart(false);
	}
	
	//grab a detail
	function get_additional_detail($key)
	{
		if(isset($this->_cart_contents[$key]))
		{
			return $this->_cart_contents[$key];
		}
		else
		{
			return false;
		}
	}
	
	// set shipping details
	function set_shipping($method, $price, $code)
	{
		if(!is_numeric($price))
		{
			return false;
		}
		
		$this->_cart_contents['shipping'] = array('method'=>$method, 'price'=> (float) $price, 'code'=>$code);
		
		//update cart - recalculate
		$this->_save_cart();
	}
	
	//remove shipping details
	function clear_shipping()
	{
		$this->_cart_contents['shipping']['method']	= false;
		$this->_cart_contents['shipping']['price']	= false;
		$this->_cart_contents['shipping']['code']	= false;
		
		$this->_save_cart();
	}
	
	function clear_payment()
	{
		$this->_cart_contents['payment'] = false;
		
		// save cart - no recalculation necessary
		$this->_save_cart(false);
	}
	
	function set_payment($module, $description)
	{
		$this->_cart_contents['payment'] = array('module'=>$module, 'description'=>$description);
		
		// save cart - no recalculation necessary
		$this->_save_cart(false);
	}
	
	
	// Use this to establish that a payment has been made (this is mostly for paypal)
	function set_payment_confirmed()
	{
		$this->_cart_contents['payment']['confirmed'] = true;
		// save cart - no recalculation necessary
		$this->_save_cart(false);
	}
	
	// This saves the confirmed order 
	function save_order() {

		$this->CI->load->model('order_model');
		$this->CI->load->model('Product_model');
		
		//prepare our data for being inserted into the database
		$save	= array();
		
		// Is this a non shippable order? 
		$none_shippable = true;
		foreach ($this->_cart_contents['items'] as $item)
		{
			if($item['shippable']==1)
			{
				$none_shippable = false;
			}
		}
		//default status comes from the config file
		if($none_shippable)
		{
			$save['status']				= $this->CI->config->item('nonship_status');
		} else {
			$save['status']				= $this->CI->config->item('order_status');
		}
		
		//if the id exists, then add it to the array $save array and remove it from the customer
		if(isset($this->_cart_contents['customer']['id']) && $this->_cart_contents['customer']['id'] != '')
		{
			$save['customer_id']	= $this->_cart_contents['customer']['id'];
		}
		
		$customer					= $this->_cart_contents['customer'];
		$ship						= $customer['ship_address'];
		$bill						= $customer['bill_address'];
		
		$save['company']			= $customer['company'];
		$save['firstname']			= $customer['firstname']; 
		$save['lastname']			= $customer['lastname'];
		$save['phone']				= $customer['phone'];
		$save['email']				= $customer['email'];
		
		$save['ship_company']		= $ship['company'];
		$save['ship_firstname']		= $ship['firstname'];
		$save['ship_lastname']		= $ship['lastname'];
		$save['ship_email']			= $ship['email'];
		$save['ship_phone']			= $ship['phone'];
		$save['ship_address1']		= $ship['address1'];
		$save['ship_address2']		= $ship['address2'];
		$save['ship_city']			= $ship['city'];
		$save['ship_zip']			= $ship['zip'];
		$save['ship_zone']			= $ship['zone'];
		$save['ship_zone_id']		= $ship['zone_id'];
		$save['ship_country']		= $ship['country'];
		$save['ship_country_id']	= $ship['country_id'];
		
		$save['bill_company']		= $bill['company'];
		$save['bill_firstname']		= $bill['firstname'];
		$save['bill_lastname']		= $bill['lastname'];
		$save['bill_email']			= $bill['email'];
		$save['bill_phone']			= $bill['phone'];
		$save['bill_address1']		= $bill['address1'];
		$save['bill_address2']		= $bill['address2'];
		$save['bill_city']			= $bill['city'];
		$save['bill_zip']			= $bill['zip'];
		$save['bill_zone']			= $bill['zone'];
		$save['bill_zone_id']		= $bill['zone_id'];
		$save['bill_country']		= $bill['country'];
		$save['bill_country_id']	= $bill['country_id'];
		
		//shipping information
		$save['shipping_method']	= $this->_cart_contents['shipping']['method'];
		$save['shipping']			= $this->_cart_contents['shipping']['price'];
		
		//add in the other charges
		$save['tax']				= $this->_cart_contents['tax'];
		//discounts
		$save['gift_card_discount'] = $this->_cart_contents['gift_card_discount'];
		$save['coupon_discount']	= $this->_cart_contents['coupon_discount'];
		$save['subtotal']			= $this->_cart_contents['cart_subtotal'];
		$save['total']				= $this->_cart_contents['cart_total'];
		
		//store the payment info
		//it's up to the payment method to remove any sensitive data from the array before this time
		if(!empty($this->_cart_contents['payment']['description']))
		{
			$save['payment_info']	= $this->_cart_contents['payment']['description'];
		}
		else
		{
			//also set the description to '' so we don't get errors anywhere else.
			//we may want to review this later and see if there is a better way from having it even come to this.
			$this->_cart_contents['payment']['description']	= '';
			$save['payment_info']	= '';
		}
		
		//save additional details
		$save['referral']		= $this->get_additional_detail('referral');
		$save['shipping_notes']	= $this->get_additional_detail('shipping_notes');
		
		//ordered_on datetime stamp
		$save['ordered_on']			= date('Y-m-d H:i:s');	
		
		//contents this is the content section serialized
		//later on if we want to add out of stock counting here is where we will
		//decrement our stock
		$contents					= array();
		
		foreach ($this->_cart_contents['items'] as $item)
		{
			$contents[]				= serialize($item);
		}
		
		// save the order content
		$order_id					= $this->CI->order_model->save_order($save, $contents);
	
		// dont do anything else if the order failed to save
		if(!$order_id) return false;

						
		// Process any per-item operations
		$download_package = array(); //create digital package array
		foreach ($this->_cart_contents['items'] as $item)
		{
			
			// Process Gift Card purchase				
			if($this->gift_cards_enabled && isset($item['gc_info'])) 
			{
				$gc_data = array();
				$gc_data['order_number'] = $order_id;
				$gc_data['beginning_amount'] = $item['price'];
				$gc_data['code'] = $item['code'];
				$gc_data= array_merge($gc_data, $item['gc_info']);
				
				$this->CI->Gift_card_model->save_card($gc_data);
				
				//send the recipient a message
				$this->CI->Gift_card_model->send_notification($gc_data);	
			}

			
			// Process Downloadable Products
			if(!empty($item['file_list']))
			{
				// compile a list of all the items that can be downloaded for this order
				$download_package[] = $item['file_list'];
			}
			
			//deduct any quantities from the database
			if(!$item['is_gc'])
			{
				$product		= $this->CI->Product_model->get_product($item['id']);
				$new_quantity	= intval($product->quantity) - intval($item['quantity']);
				$product_quantity	= array('id'=>$product->id, 'quantity'=>$new_quantity);
				$this->CI->Product_model->save($product_quantity);
			}
		}
		//add the digital packages to the database
		if(!empty($download_package))
		{
			// create the record, send the email
			$this->CI->Digital_Product_model->add_download_package($download_package, $order_id);
		}
			
			

		// update the balance of any gift cards used to purchase the order
		if($this->gift_cards_enabled && isset($this->_cart_contents['gc_list']))
		{
			$this->CI->Gift_card_model->update_used_card_balances($this->_cart_contents['gc_list']);
		}			
		
		// touch any used product coupons (increment usage)
		if(isset($this->_cart_contents['applied_coupons']))
		{
			foreach($this->_cart_contents['applied_coupons'] as $code=>$content)
				$this->CI->Coupon_model->touch_coupon($code);
		}
		
		// touch free shipping coupon
		if($this->_cart_contents['free_shipping_coupon'])
		{
			$this->CI->Coupon_model->touch_coupon($this->_cart_contents['free_shipping_coupon']);
		}
		
		// touch whole order coupon
		if(isset($this->_cart_contents['whole_order_discount_cp']))
		{
			$this->CI->Coupon_model->touch_coupon($this->_cart_contents['whole_order_discount_cp']);
		}
		
		
		
		return $order_id;
	}
	
	/**
	 * Cart Items
	 *
	 * Returns the cart items list
	 *
	 * @access	public
	 * @return	array
	 */
	function contents()
	{
		return $this->_cart_contents['items'];
	}
	
	/**
	 *  Retrieve Properties
	 *
	 * @access	public
	 * @return  float
	 */
	 
	function taxable_total()
	{
		return $this->_cart_contents['taxable_total'];
	}

	function total()
	{
		return round($this->_cart_contents['cart_total'], 2);
	}
	
	function subtotal()
	{
		return $this->_cart_contents['cart_subtotal'];
	}
	function group_discount()
	{
		return $this->_cart_contents['group_discount'];
	}
	function coupon_discount()
	{
		return $this->_cart_contents['coupon_discount'];
	}
	
	function discounted_subtotal()
	{
		return $this->_cart_contents['cp_discounted_subtotal'];
	}
	function gift_card_discount()
	{
		return $this->_cart_contents['gift_card_discount'];
	}
	function gift_card_balance() 
	{
		return $this->_cart_contents['gift_card_balance'];
	}
	
	function order_insurable_value()
	{
		return $this->_cart_contents['order_insurable_value'];
	}
	function order_tax()
	{
		return $this->_cart_contents['tax'];
	}
	function tax_state()
	{
		return $this->_cart_contents['tax_state'];
	}
	
	function order_weight() 
	{
		return $this->_cart_contents['order_weight'];
	}
	// return boolean
	function gift_cards_enabled()
	{
		return $this->gift_cards_enabled;
	}
	function requires_shipping()
	{
		return $this->_cart_contents['requires_shipping'];
	}
	function is_free_shipping()
	{
		if( ! $this->_cart_contents['free_shipping_coupon'])
		{
			return false;
		}
		else
		{
			return true; // if the value isn't false, it must be set
		}
	}
	
	function shipping_method()
	{
		return $this->_cart_contents['shipping'];
	}
	
	function shipping_cost()
	{
		return $this->_cart_contents['shipping']['price'];
	}
	
	function shipping_code()
	{
		return $this->_cart_contents['shipping']['code'];
	}
	
	// return array
	function payment_method()
	{
		return $this->_cart_contents['payment'];
	}
	
	

	function get_custom_charges()
	{
		return $this->_cart_contents['custom_charges'];
	}

	function customer()
	{
	
		if(!$this->_cart_contents['customer'])
		{
			return false;
		}
		else
		{
			return $this->_cart_contents['customer'];
		}
	}
	
	// Saves customer data in the cart
	function save_customer($data)
	{
		$this->_cart_contents['customer'] = $data;
		$this->_save_cart();
	}
	
	// Add a custom charge to the cart
	function add_custom_charge($key, $price)
	{
		$this->_cart_contents['custom_charges'][$key] = $price;
		$this->_save_cart();
	}
	
	function remove_custom_charge($key)
	{
		unset($this->_cart_contents['custom_charges'][$key]);
		$this->_save_cart();
	}
	

	
	/**
	 * Total Items
	 *
	 * Returns the total item count
	 *
	 * @access	public
	 * @return	integer
	 */
	function total_items()
	{
		return $this->_cart_contents['total_items'];
	}
	
	/**
	 * Destroy the cart
	 *
	 * Empties the cart
	 * 
	 *
	 * @access	public
	 * @return	null
	 */
	function destroy($keep_customer_data=true)
	{	
		// reset the cart values
		$this->_init_properties(false,$keep_customer_data);		
		// save the updated cart to our session
		$this->_save_cart(false);
	}
}