<?php include('header.php'); ?>

<div class="row">
	<div class="span12">
		<div id="myCarousel" class="carousel slide">
			<!-- Carousel items -->
			<div class="carousel-inner">
				<?php
				$active_banner	= 'active ';
				foreach($banners as $banner):?>
					<div class="<?php echo $active_banner;?>item">
						<?php
						
						$banner_image	= '<img src="'.base_url('uploads/'.$banner->image).'" />';
						if($banner->link)
						{
							$target=false;
							if($banner->new_window)
							{
								$target=' target="_blank"';
							}
							echo '<a href="'.$banner->link.'"'.$target.'>'.$banner_image.'</a>';
						}
						else
						{
							echo $banner_image;
						}
						?>
					
					</div>
				<?php 
				$active_banner = false;
				endforeach;?>
			</div>
			<!-- Carousel nav -->
			<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
			<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
		</div>
	</div>
</div>

<script type="text/javascript">
$('.carousel').carousel({
  interval: 5000
});
</script>


<div class="row">
	<?php foreach($boxes as $box):?>
	<div class="span3">
		<?php
		
		$box_image	= '<img class="responsiveImage" src="'.base_url('uploads/'.$box->image).'" />';
		if($box->link != '')
		{
			$target	= false;
			if($box->new_window)
			{
				$target = 'target="_blank"';
			}
			echo '<a href="'.$box->link.'" '.$target.' >'.$box_image.'</a>';
		}
		else
		{
			echo $box_image;
		}
		?>
	</div>
	<?php endforeach;?>
</div>

<?php include('footer.php'); ?>