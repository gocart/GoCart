
<?php if($this->Customer_model->is_logged_in(false, false)) {  ?>
<div id="logged_in_address_container">
	<div id="checkout_billing_address">
	<strong>Billing Address</strong><input style="float: right" type="button" value="Edit" onclick="pick_address('bill', 'Choose Your Billing Address')">
	
	
		<div id="bill_address_display"> 			
		 <?php $bill = $customer['bill_address'];
						  	 
				  	 if(!empty($bill['company'])) echo $bill['company'].'<br>';
				  	 echo $bill['firstname'].' '.$bill['lastname'].' &lt;'.$bill['email'].'&gt;<br>';
				  	 echo $bill['phone'].'<br>';
				  	 echo $bill['address1'].'<br>';
				  	 if(!empty($bill['address2'])) echo $bill['address2'].'<br>';
				  	 echo $bill['city'].', '.$bill['state'].' '.$bill['zip']; 
						  	 
				?>
		</div>
		<input type="checkbox" onclick="toggle_static_shipping_address(this.checked)" <?php if( $customer['ship_to_bill_address']=="true") { ?> checked="checked" <?php } ?>> Ship to this address
	</div>
		
	<div id="checkout_shipping_address" <?php if( $customer['ship_to_bill_address']=="true") { ?> style="display:none" <?php } ?>>		
	<strong>Shipping Address</strong> <input style="float: right" type="button" value="Edit" onclick="pick_address('ship', 'Choose Your Shipping Address')">
	
		<div id="ship_address_display">
				<?php 
					 $ship = $customer['ship_address'];
						  	 
				  	 if(!empty($ship['company'])) echo $ship['company'].'<br>';
				  	 echo $ship['firstname'].' '.$ship['lastname'].' &lt;'.$ship['email'].'&gt;<br>';
				  	 echo $ship['phone'].'<br>';
				  	 echo $ship['address1'].'<br>';
				  	 if(!empty($ship['address2'])) echo $ship['address2'].'<br>';
				  	 echo $ship['city'].', '.$ship['state'].' '.$ship['zip'];
	
				?>
		</div>
	</div>
</div>

<?php } else {  ?>
<table class="gc_customer_details" cellpadding="0" cellspacing="0" >
			<tr>
				<td width="50%">
					<table>
						<thead>
							<tr>
								<th colspan="2">Billing Information</th>
							</tr>
						</thead>
						<tbody>
						<?php if($this->Customer_model->is_logged_in(false, false)) :  ?>		
								<tr>
									<td>Choose Address</td>
									<td>
										<select name="billing_address_bank" onchange="set_address_from_bank('bill', this.value)">
											<option value="">- Choose An Address -</option>
											<?php foreach($customer_addresses as $address) { 
													echo '<option value="'.$address['id'].'">'. $address['entry_name'] .'</option>';
												 } ?>
										</select>
									</td>
								</tr>
						<?php endif; ?>
							<tr>
									<td>Ship to this address?</td>
									<td>
										<input type="checkbox" name="ship_to_bill_address" value="yes" id="ship_to_bill_address" onchange="toggle_shipping_address(this.checked)" <?php if(@$customer['ship_to_bill_address']!='false') echo 'checked="checked"' ?>> 
									</td>
								</tr>
							<tr>
								<td>Company: </td>
								<td>
									<?php
									$data	= array('id'=>'bill_company', 'class'=>"bill cust_fld gc_reg_input", 'name'=>'bill_company', 'value'=> @$customer['bill_address']['company']);
									echo form_input($data);
									?>
								</td>
							</tr>
							<tr>
								<td>First Name: </td>
								<td>
									<?php
									$data	= array('id'=>'bill_firstname', 'class'=>"bill cust_fld gc_reg_input", 'name'=>'bill_firstname', 'value'=> @$customer['bill_address']['firstname']);
									echo form_input($data);
									?>
								</td>
							</tr>
							<tr>
								<td>Last Name: </td>
								<td>
									<?php
									$data	= array('id'=>'bill_lastname', 'class'=>"bill cust_fld gc_reg_input", 'name'=>'bill_lastname', 'value'=>@$customer['bill_address']['lastname']);
									echo form_input($data);
									?>
								</td>
							</tr>
							<tr>
								<td>Email: </td>
								<td>
									<?php
									$data	= array('id'=>'bill_email', 'class'=>"bill cust_fld gc_reg_input", 'name'=>'bill_email', 'value'=>@$customer['bill_address']['email']);
									echo form_input($data);
									?>
								</td>
							</tr>
							<tr>
								<td>Phone: </td>
								<td>
									<?php
									$data	= array('id'=>'bill_phone', 'class'=>"bill cust_fld gc_reg_input", 'name'=>'bill_phone', 'value'=> @$customer['bill_address']['phone']);
									echo form_input($data);
									?>
								</td>
							</tr>
							<tr>
									<td>Address Line 1</td>
									<td>
										<?php
										$data	= array('id'=>'bill_address1', 'class'=>"bill cust_fld gc_reg_input", 'name'=>'bill_address1', 'value'=> @$customer['bill_address']['address1']);
										echo form_input($data);
										?>	
									</td>
								</tr>
								<tr>
									<td>Address Line 2</td>
									<td>
										<?php
										$data	= array('id'=>'bill_address2', 'class'=>"bill cust_fld gc_reg_input", 'name'=>'bill_address2', 'value'=> @$customer['bill_address']['address2']);
										echo form_input($data);
										?>	
									</td>
								</tr>
								<tr>
									<td>City</td>
									<td>
										<?php
										$data	= array('id'=>'bill_city', 'class'=>"bill cust_fld gc_reg_input", 'name'=>'bill_city', 'value'=>@$customer['bill_address']['city']);
										echo form_input($data);
										?>
									</td>
								</tr>
								<tr>
									<td>State</td>
									<td>
										<?php
										echo form_dropdown('bill_state', $this->config->item('states'), @$customer['bill_address']['state'], 'id="bill_state" class="bill cust_fld"');
										?>
									</td>
								</tr>
								<tr>
									<td>Zip</td>
									<td>
										<?php
										$data	= array('id'=>'bill_zip', 'maxlength'=>'5','class'=>"bill cust_fld gc_reg_input", 'name'=>'bill_zip', 'value'=> @$customer['bill_address']['zip']);
										echo form_input($data);
										?>
									</td>
								</tr>
								
						</tbody>
					</table>
				</td>
				<td>
					<table id="shipping_address" >
						<thead>
							<tr>
								<th colspan="2">Shipping Information </th>
							</tr>
						</thead>
						<tbody>
							<?php if($this->Customer_model->is_logged_in(false, false)) :  ?>		
								<tr>
									<td>Choose Address</td>
									<td>
										<select name="billing_address_bank" onchange="set_address_from_bank('ship', this.value)">
											<option value="">- Choose An Address -</option>
											<?php foreach($customer_addresses as $address) { 
													echo '<option value="'.$address['id'].'">'. $address['entry_name'] .'</option>';
												 } ?>
										</select>
									</td>
								</tr>
						<?php endif; ?>
							<tr>
								<td>Company: </td>
								<td>
									<?php
									$data	= array('id'=>'ship_company', 'class'=>"cust_fld gc_reg_input", 'name'=>'ship_company', 'value'=> @$customer['ship_address']['company']);
									echo form_input($data);
									?>
								</td>
							</tr>
							<tr>
								<td>First Name: </td>
								<td>
									<?php
									$data	= array('id'=>'ship_firstname', 'class'=>"cust_fld gc_reg_input", 'name'=>'ship_firstname', 'value'=> @$customer['ship_address']['firstname']);
									echo form_input($data);
									?>
								</td>
							</tr>
							<tr>
								<td>Last Name: </td>
								<td>
									<?php
									$data	= array('id'=>'ship_lastname', 'class'=>"cust_fld gc_reg_input", 'name'=>'ship_lastname', 'value'=>@$customer['ship_address']['lastname']);
									echo form_input($data);
									?>
								</td>
							</tr>
							<tr>
								<td>Email: </td>
								<td>
									<?php
									$data	= array('id'=>'ship_email', 'class'=>"cust_fld gc_reg_input", 'name'=>'ship_email', 'value'=>@$customer['ship_address']['email']);
									echo form_input($data);
									?>
								</td>
							</tr>
							<tr>
								<td>Phone: </td>
								<td>
									<?php
									$data	= array('id'=>'ship_phone', 'class'=>"cust_fld gc_reg_input", 'name'=>'ship_phone', 'value'=> @$customer['ship_address']['phone']);
									echo form_input($data);
									?>
								</td>
							</tr>
							<tr>
									<td>Address Line 1</td>
									<td>
										<?php
										$data	= array('id'=>'ship_address1', 'class'=>"cust_fld gc_reg_input", 'name'=>'ship_address1', 'value'=> @$customer['ship_address']['address1']);
										echo form_input($data);
										?>	
									</td>
								</tr>
								<tr>
									<td>Address Line 2</td>
									<td>
										<?php
										$data	= array('id'=>'ship_address2', 'class'=>"cust_fld gc_reg_input", 'name'=>'ship_address2', 'value'=> @$customer['ship_address']['address2']);
										echo form_input($data);
										?>	
									</td>
								</tr>
								<tr>
									<td>City</td>
									<td>
										<?php 
										$data	= array('id'=>'ship_city', 'class'=>"cust_fld gc_reg_input", 'name'=>'ship_city', 'value'=>@$customer['ship_address']['city']);
										echo form_input($data);
										?>
									</td>
								</tr>
								<tr>
									<td>State</td>
									<td>
										<?php
										echo form_dropdown('ship_state', $this->config->item('states'), @$customer['ship_address']['state'], 'id="ship_state" class="cust_fld"');
										?>
									</td>
								</tr>
								<tr>
									<td>Zip</td>
									<td>
										<?php
										$data	= array('id'=>'ship_zip', 'maxlength'=>'5', 'class'=>"cust_fld gc_reg_input", 'name'=>'ship_zip', 'value'=> @$customer['ship_address']['zip']);
										echo form_input($data);
										?>
									</td>
								</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
<?php } ?>