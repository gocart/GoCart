<?php include('header.php'); ?>
	
	<div class="page-header">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<?php if(!empty($category->description)): ?>
	<div class="row">
		<div class="span12"><?php echo $category->description; ?></div>
	</div>
	<?php endif; ?>
	
	
	<?php if((!isset($subcategories) || count($subcategories)==0) && (count($products) == 0)):?>
		<div class="alert alert-info">
			<a class="close" data-dismiss="alert">×</a>
			<?php echo lang('no_products');?>
		</div>
	<?php endif;?>
	

	<div class="row">
		<?php if(isset($subcategories) && count($subcategories) > 0): ?>
		<div class="span3">
			<ul class="nav nav-list well">
				<li class="nav-header">
				Subcategories
				</li>
				
				<?php foreach($subcategories as $subcategory):?>
					<li><a href="<?php echo site_url(implode('/', $base_url).'/'.$subcategory->slug); ?>"><?php echo $subcategory->name;?></a></li>
				<?php endforeach;?>
			</ul>
		</div>
		
		<div class="span9">
		
		<?php else:?>
			<div class="span12">
		<?php endif;?>
			
			<?php if(count($products) > 0):?>
				
				<div class="pull-right" style="margin-top:20px;">
					<select id="sort_products" onchange="window.location='<?php echo site_url(uri_string());?>/?'+$(this).val();">
						<option value=''><?php echo lang('default');?></option>
						<option<?php echo(!empty($_GET['by']) && $_GET['by']=='name/asc')?' selected="selected"':'';?> value="by=name/asc"><?php echo lang('sort_by_name_asc');?></option>
						<option<?php echo(!empty($_GET['by']) && $_GET['by']=='name/desc')?' selected="selected"':'';?>  value="by=name/desc"><?php echo lang('sort_by_name_desc');?></option>
						<option<?php echo(!empty($_GET['by']) && $_GET['by']=='price/asc')?' selected="selected"':'';?>  value="by=price/asc"><?php echo lang('sort_by_price_asc');?></option>
						<option<?php echo(!empty($_GET['by']) && $_GET['by']=='price/desc')?' selected="selected"':'';?>  value="by=price/desc"><?php echo lang('sort_by_price_desc');?></option>
					</select>
				</div>
				<div style="float:left;"><?php echo $this->pagination->create_links();?></div>
				<br style="clear:both;"/>
				<ul class="thumbnails category_container">
				<?php foreach($products as $product):?>
					<li class="span3 product">
						<?php
						$photo	= theme_img('no_picture.png', lang('no_image_available'));
						$product->images	= array_values($product->images);
				
						if(!empty($product->images[0]))
						{
							$primary	= $product->images[0];
							foreach($product->images as $photo)
							{
								if(isset($photo->primary))
								{
									$primary	= $photo;
								}
							}

							$photo	= '<img src="'.base_url('uploads/images/thumbnails/'.$primary->filename).'" alt="'.$product->seo_title.'"/>';
						}
						?>
						<a class="thumbnail" href="<?php echo site_url(implode('/', $base_url).'/'.$product->slug); ?>">
							<?php echo $photo; ?>
						</a>
						<h5 style="margin-top:5px;"><a href="<?php echo site_url(implode('/', $base_url).'/'.$product->slug); ?>"><?php echo $product->name;?></a></h5>
						<?php if($product->excerpt != ''): ?>
						<div class="excerpt"><?php echo $product->excerpt; ?></div>
						<?php endif; ?>
						<div>
						<div>
							<?php if($product->saleprice > 0):?>
								<span class="price-slash"><?php echo lang('product_reg');?> <?php echo format_currency($product->price); ?></span>
								<span class="price-sale"><?php echo lang('product_sale');?> <?php echo format_currency($product->saleprice); ?></span>
							<?php else: ?>
								<span class="price-reg"><?php echo lang('product_price');?> <?php echo format_currency($product->price); ?></span>
							<?php endif; ?>
						</div>
		                    <?php if((bool)$product->track_stock && $product->quantity < 1) { ?>
								<div class="stock_msg"><?php echo lang('out_of_stock');?></div>
							<?php } ?>
						</div>
				
					</li>
				<?php endforeach?>
				</ul>
			<?php endif;?>
		</div>
	</div>

<script type="text/javascript">
	window.onload = function(){
		$('.product').equalHeights();
	}
</script>
<?php include('footer.php'); ?>