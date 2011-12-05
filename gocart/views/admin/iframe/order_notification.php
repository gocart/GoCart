<?php include('header.php');?>

<?php if(isset($finished)) : ?>

<script type="text/javascript"> $(function() { parent.location.reload(); }); </script>

<?php else : ?>

<script type="text/javascript">

$(function() {
 	/// This is a workaround for a problem related to loading the colorbox more than once.
 	//   Calling the mce initializer here works the first time, then breaks each subsequent time.
        window.setTimeout('init_mce()', 100)
});

function init_mce()
{
	
	$('#content_editor').tinymce({
			// Location of TinyMCE script
			script_url : '<?php echo base_url('js/jquery/tiny_mce/tiny_mce.js');?>',
			theme : "simple",
			content_css : "<?php echo base_url($this->config->item('admin_folder').'/css/styles.css'); ?>",
			width: 450,
			height: 200
	});
}

// store message content in JS to eliminate the need to do an ajax call with every selection
var messages = { 
	<?php foreach($msg_templates as $msg)
		  {
		  		echo $msg['id'] .':{subject:"'. $msg['subject'] .'",content:"'. $msg['content'] .'"},';
		  }
	?>
}

// store customer name information, so names are indexed by email
var customer_names = {
<?php 

	echo '"'.$order->email.'":"'.$order->firstname.' '.$order->lastname.'",';
	echo '"'.$order->ship_email.'":"'.$order->ship_firstname.' '.$order->ship_lastname.'",';
	echo '"'.$order->bill_email.'":"'.$order->bill_firstname.' '.$order->bill_lastname.'",';
?>
}

// use our customer names var to update the customer name in the template
function update_name()
{
	if($('#canned_messages').val().length>0)
	{
		set_canned_message($('#canned_messages').val());
	}
}

function set_canned_message(id)
{
	// update the customer name variable before setting content	
	$('#msg_subject').val(messages[id]['subject'].replace(/{customer_name}/g, customer_names[$('#recipient_name').val()]));
	$('#content_editor').val(messages[id]['content'].replace(/{customer_name}/g, customer_names[$('#recipient_name').val()]));
}

</script>
<div style="text-align:left">
<form id="msg_form" action="<?php echo site_url($this->config->item('admin_folder').'/orders/send_notification');?>" method="post" />
<input type="hidden" name="send" value="true">
<?php if(!empty($errors)) : ?>
<div id="err_box" class="error">
	<?php echo $errors ?>
</div>
<?php endif; ?>
<table cellspacing="10">
<tr>
	<th align="left"><?php echo lang('message_templates');?></th>
</tr>
<tr>
	<td>
		<select id="canned_messages" onchange="set_canned_message(this.value)">
			<option><?php echo lang('select_canned_message');?></option>
			<?php foreach($msg_templates as $msg)
			{
				echo '<option value="'.$msg['id'].'">'.$msg['name'].'</option>';
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<th align="left"><?php echo lang('recipient');?></th>
</tr>
<tr>
	<td><select name="recipient" onchange="update_name()" id="recipient_name">
		<?php 
			if(!empty($order->email))
			{
				echo '<option value="'.$order->email.'">'.lang('account_main_email').' ('.$order->email.')';
			}
			if(!empty($order->ship_email))
			{
				echo '<option value="'.$order->ship_email.'">'.lang('shipping_email').' ('.$order->ship_email.')';
			}
			if($order->bill_email != $order->ship_email)
			{
				echo '<option value="'.$order->bill_email.'">'.lang('billing_email').' ('.$order->bill_email.')';
			}
		?>
	</select></td>
</tr>
<tr>
	<th align="left"><?php echo lang('subject');?></th>
</tr>
<tr>
	<td><input type="text" name="subject" size="40" id="msg_subject" class="gc_tf1"/></td>
</tr>
<tr>
	<th align="left"><?php echo lang('message');?></th>
</tr>
<tr>
	<td><textarea id="content_editor" name="content" ></textarea></td>
</tr>
<tr>
	<td><a onclick="$('#msg_form').trigger('submit');" class="button"><?php echo lang('send_message');?></a></td>
</tr>

</table>
</form>
</div>
<?php endif; ?>

<?php include('footer.php');