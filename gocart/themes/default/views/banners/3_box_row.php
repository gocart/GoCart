<div class="row">
	<?php foreach($banners as $banner):?>
	<div class="span4">
		<?php
		
		$box_image	= '<img class="responsiveImage" src="'.base_url('uploads/'.$banner->image).'" />';
		if($banner->link != '')
		{
			$target	= false;
			if($banner->new_window)
			{
				$target = 'target="_blank"';
			}
			echo '<a href="'.$banner->link.'" '.$target.' >'.$box_image.'</a>';
		}
		else
		{
			echo $box_image;
		}
		?>
	</div>
	<?php endforeach;?>
</div>