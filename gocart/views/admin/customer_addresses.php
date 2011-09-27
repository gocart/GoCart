<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to delete this address?');
}
</script>
<div class="button_set">
	<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder');?>/customers/address_form/<?php echo $customer->id;?>">Add Address</a>
</div>
<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<?php /*<th>ID</th> uncomment this if you want it*/ ?>
			<th class="gc_cell_left">Name/Company</th>
			<th>Contact</th>
			<th>Address</th>
			<th>Locality</th>
			<th>Country</th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	<tbody>
	<?php echo (count($addresses) < 1)?'<tr><td style="text-align:center;" colspan="6">There are currently no addresses.</td></tr>':''?>
<?php foreach ($addresses as $address):
		$f = $address['field_data'];
?>
		<tr>
			<td>
				<?php echo $f['lastname']; ?>, <?php echo $f['firstname']; ?>
				<?php echo (!empty($f['company']))?'<br/>'.$f['company']:'';?>
			</td>
			
			<td>
				<?php echo  $f['phone']; ?><br/>
				<a href="mailto:<?php echo  $f['email'];?>"><?php echo  $f['email']; ?></a>
			</td>
			
			<td>
				<?php echo $f['address1'];?>
				<?php echo (!empty($f['address2']))?'<br/>'.$f['address2']:'';?>
			</td>
			
			<td>
				<?php echo $f['city'];?>, <?php echo $f['zone'];?> <?php echo $f['zip'];?> 
			</td>
			
			<td><?php echo $f['country'];?></td>
			
			<td class="gc_cell_right list_buttons">
				<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder');?>/customers/delete_address/<?php echo $customer->id;?>/<?php echo  $address['id']; ?>" onclick="return areyousure();">Delete</a>				
				<a href="<?php echo  base_url(); ?><?php echo $this->config->item('admin_folder');?>/customers/address_form/<?php echo $customer->id;?>/<?php echo  $address['id']; ?>">Edit</a>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php include('footer.php');