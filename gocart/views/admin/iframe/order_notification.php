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
			script_url : '/assets/js/jquery/tiny_mce/tiny_mce.js',
			theme : "simple",
			content_css : "<?php echo base_url().$this->config->item('admin_folder').'/css/styles.css' ?>",
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
<?php if(!empty($order['customer']['email']))
{
	echo '"'.$order['customer']['email'].'":"'.$order['customer']['firstname'].' '.$order['customer']['lastname'].'",';
}
if(!empty($order['customer']['ship_address']['email']))
{
	echo '"'.$order['customer']['ship_address']['email'].'":"'.$order['customer']['ship_address']['firstname'].' '.$order['customer']['ship_address']['lastname'].'",';
}
if($order['customer']['bill_address']['email'] != $order['customer']['ship_address']['email'])
{
	echo '"'.$order['customer']['bill_address']['email'].'":"'.$order['customer']['bill_address']['firstname'].' '.$order['customer']['bill_address']['lastname'].'",';
} ?>
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
<form id="msg_form" action="<?php echo base_url().$this->config->item('admin_folder') ?>/orders/send_notification" method="post">
<input type="hidden" name="send" value="true">
<?php if(!empty($errors)) : ?>
<div id="err_box" class="error">
	<?php echo $errors ?>
</div>
<?php endif; ?>
<table cellspacing="10">
<tr>
	<th align="left">Message Templates</th>
</tr>
<tr>
	<td>
		<select id="canned_messages" onchange="set_canned_message(this.value)">
			<option>- Select Canned Message -</option>
			<?php foreach($msg_templates as $msg)
			{
				echo '<option value="'.$msg['id'].'">'.$msg['name'].'</option>';
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<th align="left">Recipient</th>
</tr>
<tr>
	<td><select name="recipient" onchange="update_name()" id="recipient_name">
		<?php 
			if(!empty($order['customer']['email']))
			{
				echo '<option value="'.$order['customer']['email'].'">Account Main Email ('.$order['customer']['email'].')';
			}
			if(!empty($order['customer']['ship_address']['email']))
			{
				echo '<option value="'.$order['customer']['ship_address']['email'].'">Shipping Email ('.$order['customer']['ship_address']['email'].')';
			}
			if($order['customer']['bill_address']['email'] != $order['customer']['ship_address']['email'])
			{
				echo '<option value="'.$order['customer']['bill_address']['email'].'">Billing Email ('.$order['customer']['bill_address']['email'].')';
			}
		?>
	</select></td>
</tr>
<tr>
	<th align="left">Subject</th>
</tr>
<tr>
	<td><input type="text" name="subject" size="40" id="msg_subject" class="gc_tf1"/></td>
</tr>
<tr>
	<th align="left">Message</th>
</tr>
<tr>
	<td><textarea id="content_editor" name="content" ></textarea></td>
</tr>
<tr>
	<td><a onclick="$('#msg_form').trigger('submit');" class="button">Send Message</a></td>
</tr>

</table>
</form>
</div>
<?php endif; ?>

<?php include('footer.php');?>