<?php
Class Product_model extends CI_Model
{
		
	// we will store the group discount formula here
	// and apply it to product prices as they are fetched 
	var $group_discount_formula = false;
	
	function __construct()
	{
		parent::__construct();
		
		// check for possible group discount 
		$customer = $this->session->userdata('customer');
		if(isset($customer['group_discount_formula'])) 
		{
			$this->group_discount_formula = $customer['group_discount_formula'];
		}
	}

	function get_products($category_id = false, $limit = false, $offset = false)
	{
		//if we are provided a category_id, then get products according to category
		if ($category_id)
		{
			$result = $this->db->select('category_products.*')->from('category_products')->join('products', 'category_products.product_id=products.id')->where(array('category_id'=>$category_id, 'enabled'=>1))->limit($limit)->offset($offset)->get()->result();
			
			//$this->db->order_by('sequence', 'ASC');
			//$result	= $this->db->get_where('category_products', array('enabled'=>1,'category_id'=>$category_id), $limit, $offset);
			//$result	= $result->result();

			$contents	= array();
			$count		= 0;
			foreach ($result as $product)
			{

				$contents[$count]	= $this->get_product($product->product_id);
				$count++;
			}

			return $contents;
		}
		else
		{
			//sort by alphabetically by default
			$this->db->order_by('name', 'ASC');
			$result	= $this->db->get('products');
			//apply group discount
			$return = $result->result();
			if($this->group_discount_formula) 
			{
				foreach($return as &$product) {
					eval('$product->price=$product->price'.$this->group_discount_formula.';');
				}
			}
			return $return;
		}

	}

	function count_products($id)
	{
		$this->db->select('product_id')->from('category_products')->join('products', 'category_products.product_id=products.id')->where(array('category_id'=>$id, 'enabled'=>1))->count_all_results();
	}

	function get_product($id, $sub=true)
	{
		$result	= $this->db->get_where('products', array('id'=>$id))->row();
		if(!$result)
		{
			return false;
		}
		
		$result->categories = $this->get_product_categories($result->id);
		
		// group discount?
		if($this->group_discount_formula) 
		{
			eval('$result->price=$result->price'.$this->group_discount_formula.';');
		}
		return $result;
	}

	function get_product_categories($id)
	{
		$cats	= $this->db->where('product_id', $id)->get('category_products')->result();
		
		$categories = array();
		foreach ($cats as $c)
		{
			$categories[] = $c->category_id;
		}
		return $categories;
	}

	function get_slug($id)
	{
		return $this->db->get_where('products', array('id'=>$id))->row()->slug;
	}

	function check_slug($str, $id=false)
	{
		$this->db->select('slug');
		$this->db->from('products');
		$this->db->where('slug', $str);
		if ($id)
		{
			$this->db->where('id !=', $id);
		}
		$count = $this->db->count_all_results();

		if ($count > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function save($product, $options=false, $categories=false)
	{
		if ($product['id'])
		{
			$this->db->where('id', $product['id']);
			$this->db->update('products', $product);

			$id	= $product['id'];
		}
		else
		{
			$this->db->insert('products', $product);
			$id	= $this->db->insert_id();
		}

		//loop through the product options and add them to the db
		if($options !== false)
		{
			$obj =& get_instance();
			$obj->load->model('Option_model');

			// wipe the slate
			$obj->Option_model->clear_options($id);

			// save edited values
			$count = 1;
			foreach ($options as $option)
			{
				$values = $option['values'];
				unset($option['values']);
				$option['product_id'] = $id;
				$option['sequence'] = $count;

				$obj->Option_model->save_option($option, $values);
				$count++;
			}
		}
		
		if($categories !== false)
		{
			if($product['id'])
			{
				//get all the categories that the product is in
				$cats	= $this->get_product_categories($id);
				
				//eliminate categories that products are no longer in
				foreach($cats as $c)
				{
					if(!in_array($c, $categories))
					{
						$this->db->delete('category_products', array('product_id'=>$id,'category_id'=>$c));
					}
				}
				
				//add products to new categories
				foreach($categories as $c)
				{
					if(!in_array($c, $cats))
					{
						$this->db->insert('category_products', array('product_id'=>$id,'category_id'=>$c));
					}
				}
			}
			else
			{
				//new product add them all
				foreach($categories as $c)
				{
					$this->db->insert('category_products', array('product_id'=>$id,'category_id'=>$c));
				}
			}
		}
		
		//return the product id
		return $id;
	}
	
	function delete_product($id)
	{
		// delete product 
		$this->db->where('id', $id);
		$this->db->delete('products');

		//delete references in the product to category table
		$this->db->where('product_id', $id);
		$this->db->delete('category_products');
		
		// delete coupon reference
		$this->db->where('product_id', $id);
		$this->db->delete('coupons_products');

	}

	function add_product_to_category($product_id, $optionlist_id, $sequence)
	{
		$this->db->insert('product_categories', array('product_id'=>$product_id, 'category_id'=>$category_id, 'sequence'=>$sequence));
	}

	function search_products($term, $limit=false, $offset=false)
	{
		$results		= array();

		//I know this is the round about way of doing things and is not the fastest. but it is thus far the easiest.

		//this one counts the total number for our pagination
		$this->db->like('name', $term);
		$this->db->or_like('description', $term);
		$this->db->or_like('excerpt', $term);
		$this->db->or_like('sku', $term);
		$results['count']	= $this->db->count_all_results('products');

		//this one gets just the ones we need.
		$this->db->like('name', $term);
		$this->db->or_like('description', $term);
		$this->db->or_like('excerpt', $term);
		$this->db->or_like('sku', $term);
		$results['products']	= $this->db->get('products', $limit, $offset)->result();
		return $results;
	}

	// Build a cart-ready product array
	function get_cart_ready_product($id, $quantity=false)
	{
		$db_product			= $this->get_product($id);
		if( ! $db_product)
		{
			return false;
		}
		
		$product = array();
		
		if ($db_product->saleprice == 0.00) { 
			$product['price']	= $db_product->price;
		}
		else
		{
			$product['price']	= $db_product->saleprice;
		}
		
		$product['base_price'] 		= $product['price']; // price gets modified by options, show the baseline still...
		$product['id']				= $db_product->id;
		$product['name']			= $db_product->name;
		$product['sku']				= $db_product->sku;
		$product['excerpt']			= $db_product->excerpt;
		$product['weight']			= $db_product->weight;
		$product['shippable']	 	= $db_product->shippable;
		$product['taxable']			= $db_product->taxable;
		$product['fixed_quantity']	= $db_product->fixed_quantity;
		$product['track_stock']		= $db_product->track_stock;
		$product['options']			= array();
		
		// Some products have n/a quantity, such as downloadables	
		if (!$quantity || $quantity <= 0 || $db_product->fixed_quantity==1)
		{
			$product['quantity'] = 1;
		} else {
			$product['quantity'] = $quantity;
		}

		
		// attach list of associated downloadables
		$product['file_list']	= $this->Digital_Product_model->get_associations_by_product($id);
		
		return $product;
	}
}