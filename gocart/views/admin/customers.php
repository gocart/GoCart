<?php include('header.php'); ?>
<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to delete this user?');
}
</script>
<div class="button_set">
	<a href="<?php echo site_url($this->config->item('admin_folder').'/customers/export_xml');?>">Download Customer List (XML)</a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/customers/get_subscriber_list');?>">Download Email Subscriber List (CSV)</a>
	<a href="<?php echo site_url($this->config->item('admin_folder').'/customers/form'); ?>">Add New Customer</a>
</div>

<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<?php /*<th>ID</th> uncomment this if you want it*/ ?>
			
			<?php
			if($by=='ASC')
			{
				$by='DESC';
			}
			else
			{
				$by='ASC';
			}
			?>
			
			<th class="gc_cell_left"><a href="<?php echo site_url($this->config->item('admin_folder').'/customers/index/lastname/');?>/<?php echo ($field == 'lastname')?$by:'';?>">Last Name</a></th>
			<th><a href="<?php echo site_url($this->config->item('admin_folder').'/customers/index/firstname/');?>/<?php echo ($field == 'firstname')?$by:'';?>">First Name</a></th>
			<th><a href="<?php echo site_url($this->config->item('admin_folder').'/customers/index/email/');?>/<?php echo ($field == 'email')?$by:'';?>">Email</a></th>
			<th>Active</th>
			<th class="gc_cell_right"></th>
		</tr>
	</thead>
	
	<tbody>
		<?php
		$page_links	= $this->pagination->create_links();
		
		if($page_links != ''):?>
		<tr><td colspan="5" style="text-align:center"><?php echo $page_links;?></td></tr>
		<?php endif;?>
		<?php echo (count($customers) < 1)?'<tr><td style="text-align:center;" colspan="5">There are currently no customers.</td></tr>':''?>
<?php foreach ($customers as $customer):?>
		<tr class="gc_row">
			<?php /*<td style="width:16px;"><?php echo  $customer->id; ?></td>*/?>
			<td><?php echo  $customer->lastname; ?></td>
			<td class="gc_cell_left"><?php echo  $customer->firstname; ?></td>
			<td><a href="mailto:<?php echo  $customer->email;?>"><?php echo  $customer->email; ?></a></td>
			<td>
				<?php if($customer->active == 1)
				{
					echo 'Yes';
				}
				else
				{
					echo 'No';
				}
				?>
			</td>
			<td class="gc_cell_right list_buttons">
				<a href="<?php echo site_url($this->config->item('admin_folder').'/customers/delete/'.$customer->id); ?>" onclick="return areyousure();">Delete</a>				
				<a href="<?php echo site_url($this->config->item('admin_folder').'/customers/form/'.$customer->id); ?>">Edit</a>
				<a href="<?php echo site_url($this->config->item('admin_folder').'/customers/addresses/'.$customer->id); ?>">Addresses</a>
			</td>
		</tr>
<?php endforeach;
		if($page_links != ''):?>
		<tr><td colspan="5" style="text-align:center"><?php echo $page_links;?></td></tr>
		<?php endif;?>
	</tbody>
</table>

<?php include('footer.php'); ?>