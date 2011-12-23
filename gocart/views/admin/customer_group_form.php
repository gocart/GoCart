<?php include('header.php');

$f_id = form_hidden('id', set_value('id', $id));
$f_name = form_input('name', set_value('name', $name), 'class="gc_tf1"');
$f_discount = form_input('discount', set_value('discount', $discount), 'class="gc_tf1"');
$f_discount_type = form_dropdown('discount_type', array('percent'=>'percent','fixed'=>'fixed'), set_value('discount_type', $discount_type));

echo form_open($this->config->item('admin_folder').'/customers/edit_group/'.$id); 

?>

<div class="button_set">
   <input type="submit" value="<?php echo lang('save');?>" />
</div>
	
<div id="gc_tabs">
	<ul>
		<li><a href="#gc_coupon_attributes"><?php echo lang('attributes');?></a></li>
	</ul>	
	<div id="attributes">
		<h2></h2>
			<table>
				<tr>
					<td><?php echo lang('group_name');?> </td>
					<td>
	                <?php echo $f_id; 
	                	  echo $f_name; ?>
	                 </td>
				</tr>
				<tr>
					<td><?php echo lang('discount');?> </td>
					<td>
					<?php echo $f_discount ?>
					</td>
				</tr>
				<tr>
				  <td><?php echo lang('discount_type');?></td>
				  <td>
				  	<?php echo $f_discount_type  ?>
				  </td>
  				</tr>
			</table>
		<br/>
	</div>
</div>
</form>

<script>
$(function(){
	$('#gc_tabs').tabs();
});
</script>

<?php include('footer.php') ?>