<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to delete this product?');
}
</script>
<div class="button_set">
	<a href="#" onclick="$('#bulk_form').submit(); return false;">Bulk Save</a>
	<a href="<?php echo base_url(); ?><?php echo $this->config->item('admin_folder');?>/products/form">Add New Product</a>
</div>

<?php echo secure_form_open($this->config->item('admin_folder').'/products/bulk_save', array('id'=>'bulk_form'));?>
	<table class="gc_table" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="gc_cell_left">SKU</th>
				<th>Name</th>
				<th style="width:60px;">Price</th>
				<th style="width:60px;">Sale</th>
				<th style="width:60px;">Availability</th>
				<th class="gc_cell_right"></th>
			</tr>
		</thead>
		<tbody>
		<?php echo (count($products) < 1)?'<tr><td style="text-align:center;" colspan="6">There are currently no products.</td></tr>':''?>
	<?php foreach ($products as $product):?>
			<tr class="gc_row">
				<td><?php echo form_input(array('name'=>'product['.$product->id.'][sku]','value'=>form_decode($product->sku), 'class'=>'gc_tf3'));?></td>
				<td><?php echo form_input(array('name'=>'product['.$product->id.'][name]','value'=>form_decode($product->name), 'class'=>'gc_tf3'));?></td>
				<td><?php echo form_input(array('name'=>'product['.$product->id.'][price]', 'value'=>set_value('price', $product->price), 'class'=>'gc_tf3'));?></td>
				<td><?php echo form_input(array('name'=>'product['.$product->id.'][saleprice]', 'value'=>set_value('saleprice', $product->saleprice), 'class'=>'gc_tf3'));?></td>
				<td>
					<?php
					 	$options = array(
			                  '1'  => 'In Stock',
			                  '0'    => 'Out of Stock'
			                );

						echo form_dropdown('product['.$product->id.'][in_stock]', $options, set_value('in_stock',$product->in_stock));
					?>
				</td>
				<td class="gc_cell_right list_buttons">
					<a href="<?php echo base_url(); ?><?php echo $this->config->item('admin_folder');?>/products/delete/<?php echo  $product->id; ?>" onclick="return areyousure();">Delete</a>
				
					<a href="<?php echo base_url(); ?><?php echo $this->config->item('admin_folder');?>/products/form/<?php echo  $product->id; ?>">Edit</a>
				
					<a href="<?php echo base_url(); ?><?php echo $this->config->item('admin_folder');?>/products/form/<?php echo  $product->id; ?>/1">Copy</a>
				</td>
			</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
</form>
<div class="button_set">
	<a href="#" onclick="$('#bulk_form').submit(); return false;">Bulk Save</a>
	<a href="<?php echo base_url(); ?><?php echo $this->config->item('admin_folder');?>/products/form">Add New Product</a>
</div>
<?php include('footer.php'); ?>