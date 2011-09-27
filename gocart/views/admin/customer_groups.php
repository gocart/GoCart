<script type="text/javascript">
function areyousure()
{
	return confirm('Are you sure you want to delete this list?');
}

$(function(){
	$('.add_btn').button({ icons: {primary: 'ui-icon-circle-plus'},
							text: true	
						 });
	$('.cancel_btn').button({icons: {primary: 'ui-icon-circle-minus'},
								text: true
						});
	$('.save_btn').button({icons: {primary: 'ui-icon-check'},
								text: true
						});					
	
});
</script>
<style type="text/css">
	/* no scrolling on the body here*/
	body, html {
		overflow:hidden;
		background-color:#fff;
	}
</style>

<?php /* here are the universal form elements */ ?>
<div id="gc_options">
	
	<div style="height:440px; overflow-y:auto; padding:5px;">
	<table style="width:100%;">
	<tbody id="gc_option_list">
	
	<?php 
		if(isset($groups)) :
			foreach ($groups as $group):?>
				<tr id="group_<?php echo $group['id']; ?>">
					<td><span id="group_name_<?php echo $group['id']; ?>"><?php echo $group['name'];?></span></td>
					<td>
						<a onclick="delete_group(<?php echo $group['id'];?>)" class="ui-state-default ui-corner-all" style="float:right;"><span class="ui-icon ui-icon-circle-minus"></span></a>
						
						<a onclick="edit_group(<?php echo $group['id'];?>)" class="ui-state-default ui-corner-all" style="float:right; margin-right:5px;"><span class="ui-icon ui-icon-pencil"></span></a>
					</td>
				</tr>
		<?php	endforeach; ?>
       <?php else :  ?>
			
		<tr><td> There are no groups </td></tr>
       <?php endif; ?>
		</tbody>
	</table></div>
	
		<div style="text-align:center;">
			<a onclick="new_group()" class="add_btn">New Group</a>

			<a onclick="cancel_groups()" class="cancel_btn">Cancel</a>
		</div>
	</div>
</div>
<div id="gc_option_form_container">


	<div id="gc_form" class="gc_forms">
		<h2></h2>
			<table>
				<tr>
				<td>Group  Name: </td>
				<td>
                <input type="hidden" name="group_id" id="group_id" />
                <input type="text" name="group_name" id="group_name" value="" class="gc_tf1" /></td>
			</tr>
			<tr>
				<td>Price Adjust: </td>
				<td><input type="text" name="group_discount" id="group_discount" value="" class="gc_tf1"/></td>
			</tr>
			<tr>
			  <td>Adjust Type</td>
			  <td><select name="group_discount_type" id="group_discount_type">
			    <option>percent</option>
			    <option>fixed</option>
			    </select>
			  </td>
  				</tr>
			</table>

		<br/>
        <a onclick="save_form()" class="save_btn">Save Group</a>
		<a onclick="cancel()" class="cancel_btn">Cancel</a>		
	</div>

<?php /* This is where we are going to store all our hidden fields that get generated from the javascript*/?>
</div>

<script type="text/javascript">
<!--
function new_group()
{
	clear_form();
	show_form('New Customer Group');
}

function clear_form()
{
	$('#group_id').val('');
	$('#group_name').val('');
	$('#group_discount').val('');
	$('#group_discount_type').val('');
}

function cancel_groups()
{
	//close the lightbox
	$.fn.colorbox.close();
}


function show_form(title)
{	
	$('#gc_form').show();
	$('#gc_form h2').html(title);
}

function edit_group(id)
{
	//make sure everything is cleared out and do not reload the menu
	clear_form();
	
	$.post("<?php echo base_url();?><?php echo $this->config->item('admin_folder');?>/customers/get_group",
	{id:id},
		//callback
  		function(data){
   			//show the form
			show_form('Edit Customer Group');
			
			//fill in the hidden fields
			$('#group_id').val(data.id);
			$('#group_name').val(data.name);
			$('#group_discount').val(data.discount);
			$('#group_discount_type').val(data.discount_type);
			
		}, 'json');
}

function save_form()
{	
	//basic error checking
	var error	= '';
	if($('#group_name').val() == '')
	{
		error += 'You must enter a name for this group.\r\n';
	}
	
	if(error == '')
	{
		$.post("<?php echo base_url();?><?php echo $this->config->item('admin_folder');?>/customers/save_group",
		{	
			group_id: $('#group_id').val(),
			group_name: $('#group_name').val(),
			group_price: $('#group_discount').val(),
			group_type: $('#group_discount_type').val() },
			//callback
	  		function(data){
				//data is the ID returned, check if it exists, if it does, write over it
				//if it does not append it to the table
				if ( $("#group_"+data).length > 0 )
				{
					
					//edit the data within the row
					$("#group_"+data).html('<td><span id="opt_name_'+data+'">'+$('#group_name').val()+'</span></td><td><a onclick="delete_group('+data+')" class="ui-state-default ui-corner-all" style="float:right;"><span class="ui-icon ui-icon-circle-minus"></span></a><a onclick="edit_group('+data+')" class="ui-state-default ui-corner-all" style="float:right; margin-right:5px;"><span class="ui-icon ui-icon-pencil"></span></a></td>');
					
				}
				else
				{
					//add a new row to the table
					$('#gc_option_list').append('<tr id="group_'+data+'"><td><span id="opt_name_'+data+'">'+$('#group_name').val()+'</span></td><td><a onclick="delete_group('+data+')" class="ui-state-default ui-corner-all" style="float:right;"><span class="ui-icon ui-icon-circle-minus"></span></a><a onclick="edit_group('+data+')" class="ui-state-default ui-corner-all" style="float:right; margin-right:5px;"><span class="ui-icon ui-icon-pencil"></span></a></td></tr>');
				}
				//just clear it all
				cancel();
				
		});
	}
	else
	{
		alert(error);
	}
}


function delete_group(group_id)
{
	if(confirm('Are you sure you want to delete this group?'))
	{
		$.post("<?php echo base_url();?><?php echo $this->config->item('admin_folder');?>/customers/delete_group",
		{	id:group_id},
	  		function(data){
				$('#group_'+data).remove();
		});
	}
}

function cancel()
{
	hide_element('gc_form');
}



function hide_element(element)
{
	$('#'+element).hide();
}
function show_element(element)
{
	$('#'+element).show();
}
</script>