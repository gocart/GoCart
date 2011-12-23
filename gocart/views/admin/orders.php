<?php require('header.php'); 
	
	//set "code" for searches
	if(!$code)
	{
		$code = '';
	}
	else
	{
		$code = '/'.$code;
	}
	function sort_url($by, $sort, $sorder, $code, $admin_folder)
	{
		if ($sort == $by)
		{
			if ($sorder == 'asc')
			{
				$sort	= 'desc';
			}
			else
			{
				$sort	= 'asc';
			}
		}
		else
		{
			$sort	= 'asc';
		}
		$return = site_url($admin_folder.'/orders/index/'.$by.'/'.$sort.'/'.$code);
		return $return;
	}
			

	$pagination = '<tr><td class="gc_pagination" colspan="8"><table class="table_nav" style="width:100%" cellpadding="0" cellspacing="0"><tr><td style="width:50px; text-align:left;">';
 	
 	$pagination .= '</td><td style="text-align:center;">';
 	
 	$pagination .= $pages;
 	
 	
 	$pagination .= $pages;
 	$pagination .= '</td><td style="width:50px; text-align:right;">';
	$pagination .= '</td></tr></table></td></tr>';
	
	
if ($term)
{
	echo '<p id="searched_for"><div style="width:70%;float:left;"><strong>'.sprintf(lang('search_returned'), intval($total)).'</strong></div><div style="width:29% float:right; text-align:right;"><a href="'.base_url().$this->config->item('admin_folder').'/orders" class="button">'.lang('all_orders').'</a></div></p>';
	
}
?>
<?php echo form_open($this->config->item('admin_folder').'/orders', array('id'=>'search_form')); ?>
	<input type="hidden" name="term" id="search_term" value=""/>
	<input type="hidden" name="start_date" id="start_date" value=""/>
	<input type="hidden" name="end_date" id="end_date" value=""/>
</form>

<?php echo form_open($this->config->item('admin_folder').'/orders/export', array('id'=>'export_form')); ?>
	<input type="hidden" name="term" id="export_search_term" value=""/>
	<input type="hidden" name="start_date" id="export_start_date" value=""/>
	<input type="hidden" name="end_date" id="export_end_date" value=""/>
</form>

<?php echo form_open($this->config->item('admin_folder').'/orders/bulk_delete', array('id'=>'delete_form')); ?>

<table class="gc_table" cellspacing="0" cellpadding="0">
    <thead>
		<tr>
			<th class="gc_cell_left"><input type="checkbox" id="gc_check_all" /></th>
			<th><a href="<?php echo sort_url('order_number', $sort_by, $sortorder, $code, $this->config->item('admin_folder')); ?>"><?php echo lang('order')?></a></th>
			<th><a href="<?php echo sort_url('bill_lastname', $sort_by, $sortorder, $code, $this->config->item('admin_folder')); ?>"><?php echo lang('bill_to')?></a></th>
			<th><a href="<?php echo sort_url('ship_lastname', $sort_by, $sortorder, $code, $this->config->item('admin_folder')); ?>"><?php echo lang('ship_to')?></a></th>
			<th><a href="<?php echo sort_url('ordered_on', $sort_by, $sortorder, $code, $this->config->item('admin_folder')); ?>"><?php echo lang('ordered_on')?></a></th>
			<th><a href="<?php echo sort_url('status', $sort_by, $sortorder, $code, $this->config->item('admin_folder')); ?>"><?php echo lang('status')?></a></th>
			<th><a href="<?php echo sort_url('notes', $sort_by, $sortorder, $code, $this->config->item('admin_folder')); ?>"><?php echo lang('notes')?></a></th>
			<th class="gc_cell_right"></th>
	    </tr>
		<?php echo $pagination; ?>
	</thead>
 	<tfoot>
    <?php echo $pagination?>
	</tfoot>
    <tbody>
		<tr>
			<td colspan="8" class="gc_table_tools">
				<div class="gc_order_delete">
					<a onclick="submit_form();" class="button"><?php echo lang('form_delete')?></a>
				</div>
				<div class="gc_order_search">
					<?php echo lang('from')?> <input id="start_top"  value="" class="gc_tf1" type="text" /> 
						<input id="start_top_alt" type="hidden" name="start_date" />
					<?php echo lang('to')?> <input id="end_top" value="" class="gc_tf1" type="text" />
						<input id="end_top_alt" type="hidden" name="end_date" />
					<?php echo lang('term')?> <input id="top" type="text" class="gc_tf1" name="term" value="" /> 
					<span class="button_set"><a href="#" onclick="do_search('top'); return false;"><?php echo lang('search')?></a>
					<a href="#" onclick="do_export('top'); return false;"><?php echo lang('xml_export')?></a></span>
					</span>
				</div>
			</td>
		</tr>
	<?php echo (count($orders) < 1)?'<tr><td style="text-align:center;" colspan="8">'.lang('no_orders') .'</td></tr>':''?>
    <?php foreach($orders as $order): ?>
	<tr>
		<td><input name="order[]" type="checkbox" value="<?php echo $order->id; ?>" class="gc_check"/></td>
		<td><?php echo $order->order_number; ?></td>
		<td style="white-space:nowrap"><?php echo $order->bill_lastname.', '.$order->bill_firstname; ?></td>
		<td style="white-space:nowrap"><?php echo $order->ship_lastname.', '.$order->ship_firstname; ?></td>
		<td style="white-space:nowrap"><?php echo date('m/d/y h:i a', strtotime($order->ordered_on)); ?></td>
		<td>
			<div id="status_container_<?php echo $order->id; ?>" style="position:relative; font-weight:bold; padding-left:20px;">
				<span id="status_<?php echo $order->id; ?>" class="<?php echo url_title($order->status); ?>"><?php echo $order->status; ?></span>
				<img style="position:absolute; left:2px;" src="<?php echo base_url('images/edit.gif');?>" alt="edit" title="edit" onclick="edit_status(<?php echo $order->id; ?>)"/>
			</div>
			<div id="edit_status_<?php echo $order->id; ?>" style="display:none; position:relative; white-space:nowrap;">
				<?php
				
				echo form_dropdown('status', $this->config->item('order_statuses'), $order->status, 'id="status_form_'.$order->id.'"');
				?>
				<a class="button" onClick="save_status(<?php echo $order->id; ?>)"><?php echo lang('form_save')?></a>
			</div>
		</td>
		<td><div class="MainTableNotes"><?php echo $order->notes; ?></div></td>
		<td class="gc_cell_right list_buttons">
			<a href="<?php echo site_url($this->config->item('admin_folder').'/orders/view/'.$order->id);?>"><?php echo lang('form_view')?></a>
		</td>
	</tr>
    <?php endforeach; ?>
		<tr>
			<td colspan="8" class="gc_table_tools">
				<div class="gc_order_delete">
					<a onclick="submit_form();" class="button"><?php echo lang('form_delete')?></a>
				</div>
				<div class="gc_order_search">
					<?php echo lang('from')?> <input id="start_bottom"  value="" class="gc_tf1" type="text" /> 
						<input id="start_bottom_alt" type="hidden" name="start_date" />
					<?php echo lang('to')?> <input id="end_bottom" value="" class="gc_tf1" type="text" />
						<input id="end_bottom_alt" type="hidden" name="end_date" />
					<?php echo lang('term')?> <input id="top" type="text" class="gc_tf1" name="term" value="" /> 
					<span class="button_set"><a href="#" onclick="do_search('bottom'); return false;"><?php echo lang('search')?></a>
					<a href="#" onclick="do_export('bottom'); return false;"><?php echo lang('xml_export')?></a></span>
					</span>
				</div>
			</td>
		</tr>
    </tbody>
</table>

</form>
<script type="text/javascript">
$(document).ready(function(){
	$('#gc_check_all').click(function(){
		if(this.checked)
		{
			$('.gc_check').attr('checked', 'checked');
		}
		else
		{
			 $(".gc_check").removeAttr("checked"); 
		}
	});
	
	// set the datepickers individually to specify the alt fields
	$('#start_top').datepicker({dateFormat:'mm-dd-yy', altField: '#start_top_alt', altFormat: 'yy-mm-dd'});
	$('#start_bottom').datepicker({dateFormat:'mm-dd-yy', altField: '#start_bottom_alt', altFormat: 'yy-mm-dd'});
	$('#end_top').datepicker({dateFormat:'mm-dd-yy', altField: '#end_top_alt', altFormat: 'yy-mm-dd'});
	$('#end_bottom').datepicker({dateFormat:'mm-dd-yy', altField: '#end_bottom_alt', altFormat: 'yy-mm-dd'});
});

function do_search(val)
{
	$('#search_term').val($('#'+val).val());
	$('#start_date').val($('#start_'+val+'_alt').val());
	$('#end_date').val($('#end_'+val+'_alt').val());
	$('#search_form').submit();
}

function do_export(val)
{
	$('#export_search_term').val($('#'+val).val());
	$('#export_start_date').val($('#start_'+val+'_alt').val());
	$('#export_end_date').val($('#end_'+val+'_alt').val());
	$('#export_form').submit();
}

function submit_form()
{
	if($(".gc_check:checked").length > 0)
	{
		if(confirm('<?php echo lang('confirm_order_delete') ?>'))
		{
			$('#delete_form').submit();
		}
	}
	else
	{
		alert('<?php echo lang('error_no_orders_selected') ?>');
	}
}

function edit_status(id)
{
	$('#status_container_'+id).hide();
	$('#edit_status_'+id).show();
}

function save_status(id)
{
	$.post("<?php echo site_url($this->config->item('admin_folder').'/orders/edit_status'); ?>", { id: id, status: $('#status_form_'+id).val()}, function(data){
		$('#status_'+id).html('<span class="'+data+'">'+$('#status_form_'+id).val()+'</span>');
	});
	
	$('#status_container_'+id).show();
	$('#edit_status_'+id).hide();	
}
</script>


<?php include('footer.php'); ?>