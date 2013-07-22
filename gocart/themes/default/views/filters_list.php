<?php if(count($filters)>1) : ?>
<script>
function append_filter(slug)
{
	// Read the filter string
	var split = location.search.replace('?', '').split('=');
	var filterstring = '';
	if(split.length>1)
	{
		filterstring = split[1];
	}
	
	// Get a new filter string
	$.post('<?php echo base_url('cart/changefilterstring') ?>', {option:'append',value:slug, filters:filterstring}, function(newstring) {
		url = window.location.href.split('?');
		window.location = url[0] + '?filters=' + newstring;
	});
}	
</script>
<div class="row">
	<div class="span3">
<?php
		function list_filters($parent_id, $filts) 
		{ 
?>
			<ul style="list-style-type:none">
<?php			foreach ($filts[$parent_id] as $fil): ?>
				<li>
					<?php 
						  if($parent_id==0) echo "<strong>";
						  echo "<a href=\"javascript:append_filter('".$fil->slug."')\">".$fil->name."</a>"; 
						  if($parent_id==0) echo "</strong>";
					?>
				</li>			
<?php
			if (isset($filts[$fil->id]) && sizeof($filts[$fil->id]) > 0)
			{
				list_filters($fil->id, $filts);
			}
			endforeach; ?>
		  </ul>
<?php 			
		}
		
		if(isset($filters[0]))
		{
			list_filters(0, $filters);
		}
?>
	</div>
</div>
<?php endif; ?>