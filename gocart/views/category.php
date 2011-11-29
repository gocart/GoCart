<?php include('header.php'); ?>

	<?php if(!empty($category->description)): ?>
	<div class="category_description"><?php echo $category->description; ?></div>
	<?php endif; ?>
	
	
	<?php if((!isset($subcategories) || count($subcategories)==0) && (count($products) == 0)):?>
		<div class="message">
			<?php echo lang('no_products');?>
		</div>
	<?php endif;?>
	<?php
	//these are the sub categories
	if(isset($subcategories) && count($subcategories) > 0): ?>
		<?php		
		$cat_counter = 1;
		foreach($subcategories as $subcategory):
			if($cat_counter == 1):
			?>
			
			<div class="category_container">
			
			<?php endif;?>
			
			<div class="category_box">
				<div class="thumbnail">
					<a href="<?php echo site_url($subcategory->slug); ?>">
						<img src="<?php echo (!empty($subcategory->image))?base_url('uploads/images/thumbnails/'.$subcategory->image):base_url('images/nopicture.png');?>" alt="<?php echo lang('no_image_available');?>"/>
					</a>
				</div>
				<div class="product_name">
					<a href="<?php echo site_url($subcategory->slug); ?>"><?php echo $subcategory->name;?></a>
				</div>
				<?php if($subcategory->excerpt != ''): ?>
				<div class="excerpt"><?php echo $subcategory->excerpt; ?></div>
				<?php endif; ?>
			</div>
			
			<?php 
			$cat_counter++;
			if($cat_counter == 7):?>

			</div>

			<?php 
			$cat_counter = 1;
			endif;
		endforeach;
			
		if($cat_counter != 1):?>
			</div>
		<?php endif;?>
	<?php endif; ?>
	

	<?php if(count($products) > 0):?>
		<div class="clear"></div>
		<h2><?php echo lang('products');?></h2>
		<div class="pagination">
		<?php echo $this->pagination->create_links();?>
		</div>
		<?php		
		$cat_counter = 1;
		foreach($products as $product):
			if($cat_counter == 1):
			?>
			
			<div class="category_container">
			
			<?php endif;?>
			
			<div class="product_box">
				<div class="product_sku">
					<?php echo(!empty($product->sku))?'SKU: '.$product->sku:'';?>
				</div>
				
				<div class="thumbnail">
					<?php
					$photo	= '<img src="'.base_url('images/nopicture.png').'" alt="'.lang('no_image_available').'"/>';
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
					<a href="<?php echo site_url($product->slug); ?>">
						<?php echo $photo; ?>
					</a>
				</div>
				<div class="product_name">
					<a href="<?php echo site_url($product->slug); ?>"><?php echo $product->name;?></a>
				</div>
				<?php if($product->excerpt != ''): ?>
				<div class="excerpt"><?php echo $product->excerpt; ?></div>
				<?php endif; ?>
				<div>
				<div class="price_container">
					<?php if($product->saleprice > 0):?>
						<span class="price_slash"><?php echo lang('product_reg');?> <?php echo format_currency($product->price); ?></span>
						<span class="price_sale"><?php echo lang('product_sale');?> <?php echo format_currency($product->saleprice); ?></span>
					<?php else: ?>
						<span class="price_reg"><?php echo lang('product_price');?> <?php echo format_currency($product->price); ?></span>
					<?php endif; ?>
				</div>
                    <?php if($product->in_stock==0) { ?>
						<div class="stock_msg"><?php echo lang('out_of_stock');?></div>
					<?php } ?>
				</div>
			</div>
			
			<?php 
			$cat_counter++;
			if($cat_counter == 5):?>

			</div>

			<?php 
			$cat_counter = 1;
			endif;
		endforeach;
			
		if($cat_counter != 1):?>
				<br class="clear"/>
			</div>
		<?php endif;?>
		
		<div class="gc_pagination">
		<?php echo $this->pagination->create_links();?>
		</div>
	<?php endif; ?>
		
	
	<script type="text/javascript">
	$(document).ready(function(){
		$('.category_container').each(function(){
			$(this).children().equalHeights();
		});
	});
	</script>
<?php include('footer.php'); ?>