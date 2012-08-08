<div class="row">
	<div class="span12">
		<select name="enabled" class="span3">
			<option value="1"<?php echo((bool)$enabled)?' selected="selected"':'';?>><?php echo lang('enabled');?></option>
			<option value="0"<?php echo((bool)$enabled)?'':' selected="selected"';?>><?php echo lang('disabled');?></option>
		</select>
		<div class="control-group">
			<div class="controls">
				<div class="input-append">
					<input type="text" id="add_name_input" class="span3" placeholder="<?php echo lang('table_name');?>"/><button class="btn" type="button" onclick='add_new_table()'><?php echo lang('add_table');?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<ul id="tables" class="nav nav-pills nav-stacked">

</ul>

<?php
function rate_form($from, $rate, $table_count, $rate_count)
{
	?>
	
	<?php
}
?>

<script type="text/javascript">
	var rates			= <?php echo json_encode($rates);?>;
	var table_count		= 0;
	var rate_count		= 0;
	$(document).ready(function(){
		$.each(rates, function(index, value)
		{
			var table_id = table_count;
			add_table(value);
			$.each(value.rates, function(index, value)
			{
				
				add_rate(table_id, index, value);
			});
		});
	});
	function add_new_table()
	{
		var table		= new Array();
		table.name		= $('#add_name_input').val();
		table.country	= 0;
		table.method	= 0;
		
		add_table(table);
		
	}
	function add_table(value)
	{
		var rate = $('#table_form_template').html().split('var_count').join(table_count).split('var_name').join(value.name);
		$('#tables').append(rate);
		$('#country_'+table_count).val(value.country);
		$('#method_'+table_count).val(value.method);
		$('#name_'+table_count).val(value.name);
		$('#rates_'+table_count).sortable({handle:'.handle'});
		table_count++;
	}
	
	function add_rate(table, from, rate)
	{
		var rate_row = $('#rate_form_template').html().split('var_count').join(rate_count).split('var_table_count').join(table);
	
		$('#rates_'+table).append(rate_row);
		$('#from_line_'+rate_count).val(from);
		$('#rate_line_'+rate_count).val(rate);
	
		rate_count++;
	}
	
	function delete_table(id)
	{
		if(confirm('<?php echo lang('delete_table');?>'))
		{
			$('#table_'+id).remove();
		}
	}
	function delete_rate(id)
	{
		if(confirm('<?php echo lang('delete_rate');?>'))
		{
			$('#rate_'+id).remove();
		}
	}
	function toggle_table(table)
	{
		$('#table_details_'+table).toggle();
	}
	
	$('form').submit(function(){
		$('#kill_on_save').remove();
	});
</script>

<div id="kill_on_save">
	<div id="table_form_template" style="display:none">
	
		<li class="active"id="table_var_count">
			<button onclick="delete_table(var_count);" type="button" class="btn btn-danger pull-right" style="margin-top:3px; margin-right:3px;"><i class="icon-trash icon-white"></i></button>
			<a href="#" onclick="toggle_table(var_count); return false;">var_name</a>
			<ul id="table_details_var_count" class="row table_details" style="display:none; list-style-type:none;">
				<li class="span12">
					<div class="row">
						<div class="span12">
		
							<div class="row">
								<div class="span3" style="margin-top:5px;">
									<label><?php echo lang('table_name');?></label>
									<?php echo form_input(array('name'=>'rate[var_count][name]', 'class'=>'span3', 'id'=>'name_var_count'));?>
					
									<label><?php echo lang('method');?></label>
									<?php
									$options = array('price'=>lang('price'),'weight'=>lang('weight'));
									echo form_dropdown('rate[var_count][method]', $options, '', 'class="span3" id="method_var_count"');
									?>
					
									<label><?php echo lang('country');?></label>
									<?php
										echo form_dropdown('rate[var_count][country]', $countries, '', 'class="span3" id="country_var_count"');
									?>
								</div>
								<div class="span9">
									<div class="row">
										<div class="span9">
											<div class="form-inline pull-right" style="margin:10px 0px;">
												<input type="input" class="span1" placeholder="<?php echo lang('from');?>" id="from_field_var_count"/>
												<input type="input" class="span1" placeholder="<?php echo lang('rate');?>" id="rate_field_var_count"/>
												<button type="button" class="btn" onclick="add_rate(var_count, $('#from_field_var_count').val(),$('#rate_field_var_count').val()); $('#from_field_var_count').val('');$('#rate_field_var_count').val('')"><i class="icon-plus-sign"></i> <?php echo lang('add_rate');?></button>
											</div>
										</div>
									</div>
									<table class="table table-striped">
										<tbody id="rates_var_count">
										</tbody>
									</table>
								</div>
							</div>
		
						</div>
					</div>
				</li>
			</ul>
		</li>
	
	</div>

	<table style="display:none;">
		<tbody id="rate_form_template" >
			<tr id="rate_var_count" class="form-inline">
				<td><a href="#" class="handle btn"><i class="icon-align-justify"></i></a></td>
				<td><?php echo lang('from');?> </td>
				<td><input id="from_line_var_count" type="input" name="rate[var_table_count][rates][var_count][from]" class="span1"/></td>
				<td><?php echo lang('rate');?> </td>
				<td><input id="rate_line_var_count" type="input" name="rate[var_table_count][rates][var_count][rate]" class="span1"/></td>
				<td>
					<span class="pull-right">
						<button class="btn btn-danger" type="button" onclick="delete_rate(var_count)"><i class="icon-trash icon-white"></i></button>
					</span>
				</td>
			</tr>
		</tbody>
	</table>
</div>