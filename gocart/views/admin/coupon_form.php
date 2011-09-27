<?php include('header.php'); ?>

<script type="text/javascript">
$(function(){
$("#datepicker1").datepicker({dateFormat: 'mm-dd-yy', altField: '#datepicker1_alt', altFormat: 'yy-mm-dd'});
$("#datepicker2").datepicker({dateFormat: 'mm-dd-yy', altField: '#datepicker2_alt', altFormat: 'yy-mm-dd'});
});
</script>

<?php echo form_open($this->config->item('admin_folder').'/coupons/form/'.$id); ?>
<div class="button_set">
<input type="submit" value="Save Coupon"/>
</div>
<div id="gc_tabs">
	<ul>
		<li><a href="#gc_coupon_attributes">Attributes</a></li>
		<li><a href="#gc_coupon_appliesto">Applies To</a></li>
	</ul>
	
	<div id="gc_coupon_attributes">
		<div class="gc_field2">
		<label>Coupon Code: </label>
			<?php
			$data	= array('id'=>'code', 'name'=>'code', 'value'=>set_value('code', $code), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		
		<div class="gc_field2">
			<label>Times Used: </label>
			<?php
				echo @$num_uses
			?>
		</div>
		<div class="gc_field2">
			<label for="max_uses">Max Uses: </label>
			<?php
			$data	= array('id'=>'max_uses', 'name'=>'max_uses', 'value'=>set_value('max_uses', $max_uses), 'class'=>'gc_tf1');
			echo form_input($data);
			?>
		</div>
		<div class="gc_field2">
			<label for="max_product_instances">Limit Per Order: </label>
			<?php
				$data	= array('name'=>'max_product_instances', 'value'=>set_value('max_product_instances', $max_product_instances), 'class'=>'gc_tf1');
				echo form_input($data);
			?>
		</div>
		<div class="gc_field2">
			<label for="start_date">Enable On: </label>
			<?php
				$data	= array('id'=>'datepicker1', 'value'=>set_value('start_date', reverse_format($start_date)), 'class'=>'gc_tf1');
				echo form_input($data);
			?>
			<input type="hidden" name="start_date" value="<?php echo set_value('start_date', $start_date) ?>" id="datepicker1_alt" />
		</div>
		<div class="gc_field2">
			<label for="end_date">Disable On: </label>
			<?php
				$data	= array('id'=>'datepicker2', 'value'=>set_value('end_date', reverse_format($end_date)), 'class'=>'gc_tf1');
				echo form_input($data);
			?>
			<input type="hidden" name="end_date" value="<?php echo set_value('end_date', $end_date) ?>" id="datepicker2_alt" />
		</div>
		<div class="gc_field2">
			<label for="reduction_target">Coupon Type: </label>
			<?php
		 		$options = array(
                  'price'  => 'Price Discount',
				  'shipping' => 'FREE Shipping'
               	);
				echo form_dropdown('reduction_target', $options,  $reduction_target, 'id="gc_coupon_type"');
			?>
		</div>
		<div class="gc_field2" id="gc_coupon_price_fields">
			<label for="reduction_amount">Reduction Amount: </label>
			<?php
				$data	= array('id'=>'reduction_amount', 'name'=>'reduction_amount', 'value'=>set_value('reduction_amount', $reduction_amount), 'class'=>'gc_tf1');
				echo form_input($data);
				
				$options = array(
                  'percent'  => '%',
				  'fixed' => '$'
               	);
				echo ' '.form_dropdown('reduction_type', $options,  $reduction_type);
			?>
		</div>
	</div>
	
	<div id="gc_coupon_appliesto">
		<?php
	 		$options = array(
              '1' => 'Apply Coupon to Whole Order',
			  '0' => 'Apply Coupon to Select Products'
           	);
			echo form_dropdown('whole_order_coupon', $options,  set_value(0, $whole_order_coupon), 'id="gc_coupon_appliesto_fields"');
		?>
		<div id="gc_coupon_products">
			<table width="100%" border="0" style="margin-top:10px;" cellspacing="5" cellpadding="0">
			<?php echo $product_rows; ?>
			</table>
		</div>
	</div>
</div>
</form>

<script type="text/javascript">
$(document).ready(function(){
	$("#gc_tabs").tabs();
	
	if($('#gc_coupon_type').val() == 'shipping')
	{
		$('#gc_coupon_price_fields').hide();
	}
	
	$('#gc_coupon_type').bind('change keyup', function(){
		if($(this).val() == 'price')
		{
			$('#gc_coupon_price_fields').show();
		}
		else
		{
			$('#gc_coupon_price_fields').hide();
		}
	});
	
	if($('#gc_coupon_appliesto_fields').val() == '1')
	{
		$('#gc_coupon_products').hide();
	}
	
	$('#gc_coupon_appliesto_fields').bind('change keyup', function(){
		if($(this).val() == 0)
		{
			$('#gc_coupon_products').show();
		}
		else
		{
			$('#gc_coupon_products').hide();
		}
	});
});

</script>


<?php include('footer.php'); ?>
