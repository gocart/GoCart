<?php include('header.php'); ?>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	create_sortable();
});

// Return a helper with preserved width of cells
var fixHelper = function(e, ui) {
	ui.children().each(function() {
		$(this).width($(this).width());
	});
	return ui;
};

function create_sortable()
{
	$('#category_contents').sortable({
		scroll: true,
		helper: fixHelper,
		axis: 'y',
		update: function(){
			save_sortable();
		}
	});	
	$('#category_contents').sortable('enable');
}

function save_sortable()
{
	serial=$('#category_contents').sortable('serialize');
			
	$.ajax({
		url:'<?php echo site_url($this->config->item('admin_folder').'/categories/process_organization/'.$category->id);?>',
		type:'POST',
		data:serial
	});
}
//]]>
</script>

<p>Drag and drop the products in the order you would like them to appear.</p>

<table class="gc_table" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="gc_cell_left" style="width:60px;">SKU</th>
			<th>Name</th>
			<th style="width:60px;">Price</th>
			<th class="gc_cell_right" style="width:60px;">Sale</th>
		</tr>
	</thead>
	<tbody id="category_contents">
<?php foreach ($category_products as $product):?>
		<tr id="product-<?php echo $product->id;?>" class="gc_row">
			<td class="gc_cell_left"><?php echo $product->sku;?></td>
			<td><?php echo $product->name;?></td>
			<td><?php echo $product->price;?></td>
			<td class="gc_cell_right"><?php echo $product->saleprice;?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php include('footer.php');