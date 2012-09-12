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
	function sort_url($lang, $by, $sort, $sorder, $code, $admin_folder)
	{
		if ($sort == $by)
		{
			if ($sorder == 'asc')
			{
				$sort	= 'desc';
				$icon	= ' <i class="icon-chevron-up"></i>';
			}
			else
			{
				$sort	= 'asc';
				$icon	= ' <i class="icon-chevron-down"></i>';
			}
		}
		else
		{
			$sort	= 'asc';
			$icon	= '';
		}
			

		$return = site_url($admin_folder.'/orders/index/'.$by.'/'.$sort.'/'.$code);
		
		echo '<a href="'.$return.'">'.lang($lang).$icon.'</a>';

	}
	
if ($term):?>

<div class="alert alert-info">
	<?php echo sprintf(lang('search_returned'), intval($total));?>
</div>
<?php endif;?>

<style type="text/css">
	.pagination {
		margin:0px;
		margin-top:-3px;
	}
</style>
<div class="row">
	<div class="span12" style="border-bottom:1px solid #f5f5f5;">
		<div class="row">
			<div class="span4">
				<?php echo $this->pagination->create_links();?>	
			</div>
			<div class="span8">
				<?php echo form_open($this->config->item('admin_folder').'/orders/index', 'class="form-inline" style="float:right"');?>
					<fieldset>
						<input id="start_top"  value="" class="span2" type="text" placeholder="Start Date"/>
						<input id="start_top_alt" type="hidden" name="start_date" />
						<input id="end_top" value="" class="span2" type="text"  placeholder="End Date"/>
						<input id="end_top_alt" type="hidden" name="end_date" />
				
						<input id="top" type="text" class="span2" name="term" placeholder="<?php echo lang('term')?>" /> 

						<button class="btn" name="submit" value="search"><?php echo lang('search')?></button>
						<button class="btn" name="submit" value="export"><?php echo lang('xml_export')?></button>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>

<?php echo form_open($this->config->item('admin_folder').'/orders/bulk_delete', array('id'=>'delete_form', 'onsubmit'=>'return submit_form();', 'class="form-inline"')); ?>

<table class="table table-striped">
    <thead>
		<tr>
			<th><input type="checkbox" id="gc_check_all" /> <button type="submit" class="btn btn-small btn-danger"><i class="icon-trash icon-white"></i></button></th>
			<th><?php echo sort_url('order', 'order_number', $sort_by, $sort_order, $code, $this->config->item('admin_folder')); ?></th>
			<th><?php echo sort_url('bill_to', 'bill_lastname', $sort_by, $sort_order, $code, $this->config->item('admin_folder')); ?></th>
			<th><?php echo sort_url('ship_to', 'ship_lastname', $sort_by, $sort_order, $code, $this->config->item('admin_folder')); ?></th>
			<th><?php echo sort_url('ordered_on','ordered_on', $sort_by, $sort_order, $code, $this->config->item('admin_folder')); ?></th>
			<th><?php echo sort_url('status','status', $sort_by, $sort_order, $code, $this->config->item('admin_folder')); ?></th>
			<th><?php echo sort_url('total','total', $sort_by, $sort_order, $code, $this->config->item('admin_folder')); ?></th>
			<th></th>
	    </tr>
	</thead>

    <tbody>
	<?php echo (count($orders) < 1)?'<tr><td style="text-align:center;" colspan="8">'.lang('no_orders') .'</td></tr>':''?>
    <?php foreach($orders as $order): ?>
	<tr>
		<td><input name="order[]" type="checkbox" value="<?php echo $order->id; ?>" class="gc_check"/></td>
		<td><?php echo $order->order_number; ?></td>
		<td style="white-space:nowrap"><?php echo $order->bill_lastname.', '.$order->bill_firstname; ?></td>
		<td style="white-space:nowrap"><?php echo $order->ship_lastname.', '.$order->ship_firstname; ?></td>
		<td style="white-space:nowrap"><?php echo date('m/d/y h:i a', strtotime($order->ordered_on)); ?></td>
		<td style="span2">
			<?php echo form_dropdown('status', $this->config->item('order_statuses'), $order->status, 'id="status_form_'.$order->id.'" class="span2" style="float:left;"'); ?>
			<button type="button" class="btn" onClick="save_status(<?php echo $order->id; ?>)" style="float:left;margin-left:4px;"><?php echo lang('save');?></button>
		</td>
		<td><div class="MainTableNotes"><?php echo format_currency($order->total); ?></div></td>
		<td>
			<a class="btn btn-small" style="float:right;"href="<?php echo site_url($this->config->item('admin_folder').'/orders/view/'.$order->id);?>"><i class="icon-search"></i> <?php echo lang('form_view')?></a>
		</td>
	</tr>
    <?php endforeach; ?>
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
		return confirm('<?php echo lang('confirm_delete_order') ?>');
	}
	else
	{
		alert('<?php echo lang('error_no_orders_selected') ?>');
		return false;
	}
}

function save_status(id)
{
	show_animation();
	$.post("<?php echo site_url($this->config->item('admin_folder').'/orders/edit_status'); ?>", { id: id, status: $('#status_form_'+id).val()}, function(data){
		setTimeout('hide_animation()', 500);
	});
}

function show_animation()
{
	$('#saving_container').css('display', 'block');
	$('#saving').css('opacity', '.8');
}

function hide_animation()
{
	$('#saving_container').fadeOut();
}
</script>

<div id="saving_container" style="display:none;">
	<div id="saving" style="background-color:#000; position:fixed; width:100%; height:100%; top:0px; left:0px;z-index:100000"></div>
	<img id="saving_animation" src="<?php echo base_url('assets/img/storing_animation.gif');?>" alt="saving" style="z-index:100001; margin-left:-32px; margin-top:-32px; position:fixed; left:50%; top:50%"/>
	<div id="saving_text" style="text-align:center; width:100%; position:fixed; left:0px; top:50%; margin-top:40px; color:#fff; z-index:100001"><?php echo lang('saving');?></div>
</div>
<?php include('footer.php'); ?>