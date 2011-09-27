<?php require('header.php'); ?>
<h2 style="margin:0px 0px 8px 0px; padding:0px;">Best Sellers</h2>
<div id="best_sellers_form" style="margin-bottom:10px">
	From <input class="gc_tf1" type="text"  id="best_sellers_start"/>
		 <input type="hidden" name="best_sellers_start" id="best_sellers_start_alt" /> 
	to <input class="gc_tf1" type="text" id="best_sellers_end"/>
		<input type="hidden" name="best_sellers_end" id="best_sellers_end_alt" /> 
	<input type="button" value="Get Best Sellers" onclick="get_best_sellers()"/>
</div>

<div id="best_sellers">
	
</div>

<h2 style="margin:30px 0px 8px 0px; padding:0px;">Sales</h2>
<div id="sales_container">
	
</div>


<script type="text/javascript">

$(document).ready(function(){
	get_best_sellers();
	get_sales();
	$('input:button').button();
	$('#best_sellers_start').datepicker({ dateFormat: 'mm-dd-yy', altField: '#best_sellers_start_alt', altFormat: 'yy-mm-dd' });
	$('#best_sellers_end').datepicker({ dateFormat: 'mm-dd-yy', altField: '#best_sellers_end_alt', altFormat: 'yy-mm-dd' });
});

function get_best_sellers()
{
	$.post('/<?php echo $this->config->item('admin_folder');?>/reports/best_sellers',{start:$('#best_sellers_start').val(), end:$('#best_sellers_end').val()}, function(data){
		$('#best_sellers').html(data);
	});
}

function get_sales()
{
	$.post('/<?php echo $this->config->item('admin_folder');?>/reports/sales',{bah:Math.floor(Math.random( )*9999999999)}, function(data){
		$('#sales_container').html(data);
	});
}
</script>
<?php include('footer.php'); ?>