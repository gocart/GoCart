<?php include('header.php'); ?>

<?php if(!$payment_module_installed):?>
	
	<div class="alert">
		<a class="close" data-dismiss="alert">×</a>
		<strong><?php echo lang('common_note') ?>:</strong> <?php echo lang('no_payment_module_installed'); ?>
	</div>

<?php endif;?>

<?php if(!$shipping_module_installed):?>
	<div class="alert">
		<a class="close" data-dismiss="alert">×</a>
		<strong><?php echo lang('common_note') ?>:</strong> <?php echo lang('no_shipping_module_installed'); ?>
	</div>

<?php endif;?>

<h2><?php echo lang('recent_orders') ?></h2>
<table class="table table-striped">
    <thead>
		<tr>
			<th class="gc_cell_left"><?php echo lang('order_number') ?></th>
			<th><?php echo lang('bill_to') ?></th>
			<th><?php echo lang('ship_to') ?></th>
			<th><?php echo lang('ordered_on') ?></th>
			<th><?php echo lang('status') ?></th>
			<th class="gc_cell_right"><?php echo lang('notes') ?></th>
	    </tr>
	</thead>

    <tbody>
    <?php foreach($orders as $order): ?>
	<tr>
		<td  class="gc_cell_left"><a href="<?php echo site_url($this->config->item('admin_folder').'/orders/view/'.$order->id); ?>"><?php echo $order->order_number; ?></a></td>
		<td><?php echo $order->bill_lastname.', '.$order->bill_firstname; ?></td>
		<td><?php echo $order->ship_lastname.', '.$order->ship_firstname; ?></td>
		<td><?php echo format_date($order->ordered_on); ?></td>
		<td style="width:150px;">
			<?php echo $order->status ?> 
				
		</td>
		<td class="gc_cell_right"><div class="MainTableNotes"><?php echo $order->notes; ?></div></td>
	</tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="row">
	<div class="span12" style="text-align:center;">
		<a class="btn btn-large" href="<?php echo $admin_url;?>orders"><?php echo lang('view_all_orders');?></a>
	</div>
</div>


<h2><?php echo lang('recent_customers') ?></h2>
<table class="table table-striped">
	<thead>
		<tr>
			<?php /*<th>ID</th> uncomment this if you want it*/ ?>
			<th class="gc_cell_left"><?php echo lang('firstname') ?></th>
			<th><?php echo lang('lastname') ?></th>
			<th><?php echo lang('email') ?></th>
			<th class="gc_cell_right"><?php echo lang('active') ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($customers as $customer):?>
		<tr>
			<?php /*<td style="width:16px;"><?php echo  $customer->id; ?></td>*/?>
			<td class="gc_cell_left"><?php echo  $customer->firstname; ?></td>
			<td><?php echo  $customer->lastname; ?></td>
			<td><a href="mailto:<?php echo  $customer->email;?>"><?php echo  $customer->email; ?></a></td>
			<td>
				<?php if($customer->active == 1)
				{
					echo lang('yes');
				}
				else
				{
					echo lang('no');
				}
				?>
			</td>
		
		</tr>
<?php endforeach; ?>
	</tbody>
</table>


<div class="row">
	<div class="span12" style="text-align:center;">
		<a class="btn btn-large" href="<?php echo $admin_url;?>customers"><?php echo lang('view_all_customers');?></a>
	</div>
</div>
<?php include('footer.php');