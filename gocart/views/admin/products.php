<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_product');?>');
}
</script>
<div class="button_set">
	<a href="#" onclick="$('#bulk_form').submit(); return false;"><?php echo lang('bulk_save');?></a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/products/form');?>"><?php echo lang('add_new_product');?></a>
</div>

<?php echo form_open($this->config->item('admin_folder').'/products/bulk_save', array('id'=>'bulk_form'));?>
	<table class="gc_table" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="gc_cell_left"><?php echo lang('sku');?></th>
				<th><?php echo lang('name');?></th>
				<th style="width:60px;"><?php echo lang('price');?></th>
				<th style="width:60px;"><?php echo lang('saleprice');?></th>
				<th style="width:60px;"><?php echo lang('enabled');?></th>
				<th style="width:60px;"><?php echo lang('availability');?></th>
				<th class="gc_cell_right"></th>
			</tr>
		</thead>
		<tbody>
		<?php echo (count($products) < 1)?'<tr><td style="text-align:center;" colspan="6">'.lang('no_products').'</td></tr>':''?>
	<?php foreach ($products as $product):?>
			<tr class="gc_row">
				<td><?php echo form_input(array('name'=>'product['.$product->id.'][sku]','value'=>form_decode($product->sku), 'class'=>'gc_tf3'));?></td>
				<td><?php echo form_input(array('name'=>'product['.$product->id.'][name]','value'=>form_decode($product->name), 'class'=>'gc_tf3'));?></td>
				<td><?php echo form_input(array('name'=>'product['.$product->id.'][price]', 'value'=>set_value('price', $product->price), 'class'=>'gc_tf3'));?></td>
				<td><?php echo form_input(array('name'=>'product['.$product->id.'][saleprice]', 'value'=>set_value('saleprice', $product->saleprice), 'class'=>'gc_tf3'));?></td>
				<td>
					<?php
					 	$options = array(
			                  '1'	=> lang('enabled'),
			                  '0'	=> lang('disabled')
			                );

						echo form_dropdown('product['.$product->id.'][enabled]', $options, set_value('enabled',$product->enabled));
					?>
				</td>
				<td>
					<?php
					 	$options = array(
			                  '1'	=> lang('in_stock'),
			                  '0'	=> lang('out_of_stock')
			                );

						echo form_dropdown('product['.$product->id.'][in_stock]', $options, set_value('in_stock',$product->in_stock));
					?>
				</td>
				<td class="gc_cell_right list_buttons">
					<a href="<?php echo  site_url($this->config->item('admin_folder').'/products/delete/'.$product->id);?>" onclick="return areyousure();"><?php echo lang('delete');?></a>
					<a href="<?php echo  site_url($this->config->item('admin_folder').'/products/form/'.$product->id);?>"><?php echo lang('edit');?></a>
					<a href="<?php echo  site_url($this->config->item('admin_folder').'/products/form/'.$product->id.'/1');?>"><?php echo lang('copy');?></a>
				</td>
			</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
</form>
<div class="button_set">
	<a href="#" onclick="$('#bulk_form').submit(); return false;"><?php echo lang('bulk_save');?></a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/products/form');?>"><?php echo lang('add_new_product');?></a>
</div>
<?php include('footer.php'); ?>