<?php ob_start();?>
<script type="text/javascript">
var rotate;
$(document).ready(function(){
	$('.banner_container').each(function(item)
	{
		if(item != 0)
		{
			$(this).hide();
		}
	});
	if($('.banner_container').size() > 1)
	{
		rotate_banner();
	}
	
});

var cnt	= 0;

function rotate_banner()
{
	//stop the animations from going nuts when returning from minimize
	$('.banner_container:eq('+cnt+')').fadeOut();
	cnt++;
	if(cnt == $('.banner_container').size())
	{
		cnt = 0;
	}
	$('.banner_container:eq('+cnt+')').fadeIn(function(){
		setTimeout("rotate_banner()", 3000);
	});
}
</script>

<?php
$ads_javascript	= ob_get_contents();
ob_end_clean();


$additional_header_info = $ads_javascript;

include('header.php'); ?>

<div id="banners">
	<?php 
	$banner_count	= 1;
	foreach ($banners as $banner)
	{
		echo '<div class="banner_container">';
		
		if($banner->link != '')
		{
			$target	= false;
			if($banner->new_window)
			{
				$target = 'target="_blank"';
			}
			echo '<a href="'.$banner->link.'" '.$target.' >';
		}
		echo '<img class="banners_img'.$banner_count.'" src="'.base_url('uploads/'.$banner->image).'" />';
		
		if($banner->link != '')
		{
			echo '</a>';
		}

		echo '</div>';

		$banner_count++;
	}
	?>
</div><!--ads end-->

<div id="homepage_boxes">
	<?php 
	foreach ($boxes as $box)
	{
		echo '<div class="box_container">';
		
		if($box->link != '')
		{
			$target	= false;
			if($box->new_window)
			{
				$target = 'target="_blank"';
			}
			echo '<a href="'.$box->link.'" '.$target.' >';
		}
		echo '<img src="'.base_url('uploads/'.$box->image).'" />';
		
		if($box->link != '')
		{
			echo '</a>';
		}

		echo '</div>';
	}
	?>
</div>

<?php include('footer.php'); ?>