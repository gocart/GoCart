<script>
function remove_filter(slug)
{
	// Read the filter string
	var split = location.search.replace('?', '').split('=')
	if(split.length>1)
	{
		var filterstring = split[1];
	} else { return; } // no filter to remove
	
	// Get a new filter string
	$.post('<?php echo base_url('cart/changefilterstring') ?>', {option:'remove',value:slug, filters:filterstring}, function(newstring) {
		url = window.location.href.split('?');
		
		if(newstring=='')
		{
			window.location = url[0];
		} else {
			window.location = url[0] + '?filters=' + newstring;
		}
	});
}
</script>
<div class="row">
	<div class="span12">
		<div class="well">
			Filters
			<ul>
				<?php foreach($filters as $f) : ?>
					<li><?php echo $f->name ?> (<a href="javascript:remove_filter('<?php echo $f->slug ?>')">remove</a>)</li>
				<?php endforeach; ?>	
			</ul>
		</div>
	</div>
</div>