<?php include('header.php'); ?>
<style type="text/css">
	.sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
	.sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; height: 18px; }
	.sortable li>span { position: absolute; margin-left: -1.3em; margin-top:.4em; }
</style>
<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {
	$(".sortable").sortable();
	$(".sortable > span").disableSelection();
	//if the image already exists (phpcheck) enable the selector

	<?php if($id) : ?>
	//options related
	var ct	= $('#option_list').children().size();
	//create_sortable();
	set_accordion();
	
	// set initial count
	option_count = <?php echo count($product_options); ?>;
	
	<?php endif; ?>
	
	$( ".add_option" ).click(function(){
		add_option($(this).attr('rel'));
	});
	
	photos_sortable();
});

function add_product_image(data)
{
	p	= data.split('.');
	
	var photo = '<?php add_image("'+p[0]+'", "'+p[0]+'.'+p[1]+'", '', '');?>';
	$('#gc_photos').append(photo);
	$('#gc_photos').sortable('destroy');
	photos_sortable();
}

function remove_image(img)
{
	if(confirm('<?php echo lang('confirm_remove_image');?>'))
	{
		var id	= img.attr('rel')
		$('#gc_photo_'+id).remove();
	}
}

function photos_sortable()
{
	$('#gc_photos').sortable({	
		handle : '.gc_thumbnail',
		items: '.gc_photo',
		axis: 'y',
		scroll: true
	});
}

function add_option(type)
{
	//increase option_count by 1
	option_count++;
	
	$('#options_accordion').append('<?php add_option("", "'+option_count+'", "'+type+'");?>');
		
	//eliminate the add button if this is a text based option
	if(type == 'textarea' || type == 'textfield')
	{
		$('#add_item_'+option_count).remove();
		
	}
	
	add_item(type, option_count);
	
	reset_accordion();
}

function add_item(type, id)
{
	
	var count = $('#option_items_'+id+'>li').size()+1;
	
	append_html = '';
	
	if(type!='textfield' && type != 'textarea')
	{
		append_html = append_html + '<li id="value-'+id+'-'+count+'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><a onclick="if(confirm(\'<?php echo lang('confirm_remove_value');?>\')) $(\'#value-'+id+'-'+count+'\').remove()" class="ui-state-default ui-corner-all" style="float:right;"><span class="ui-icon ui-icon-circle-minus"></span></a>';
	}
	
	append_html += '<div style="margin:2px"><span><?php echo lang('name');?>: </span> <input class="req gc_tf2" type="text" name="option['+id+'][values]['+count+'][name]" value="" /> '+
	'<span><?php echo lang('value');?>: </span> <input class="req gc_tf2" type="text" name="option['+id+'][values]['+count+'][value]" value="" /> '+
	'<span><?php echo lang('weight');?>: </span> <input class="req gc_tf2" type="text" name="option['+id+'][values]['+count+'][weight]" value="" /> '+
	'<span><?php echo lang('price');?>: </span> <input class="req gc_tf2" type="text" name="option['+id+'][values]['+count+'][price]" value="" />';
	
	if(type == 'textfield')
	{
		append_html += ' <span><?php echo lang('limit');?>: </span> <input class="req gc_tf2" type="text" name="option['+id+'][values]['+count+'][limit]" value="" />';
	}

	append_html += '</div> ';
	
	if(type!='textfield' && type != 'textarea')
	{
		append_html += '</li>';
	}
	
	
	$('#option_items_'+id).append(append_html);	
	
	$(".sortable").sortable();
	$(".sortable > span").disableSelection();
	
	
}

function remove_option(id)
{
	if(confirm('<?php echo lang('confirm_remove_option');?>'))
	{
		$('#option-'+id).remove();
		
		option_count --;
		
		reset_accordion();
	}
}

function reset_accordion()
{
	$( "#options_accordion" ).accordion('destroy');
	$('.option_item_form').sortable('destroy');
	set_accordion();
}

function set_accordion(){
	
	var stop = false;
	$( "#options_accordion h3" ).click(function( event ) {
		if ( stop ) {
			event.stopImmediatePropagation();
			event.preventDefault();
			stop = false;
		}
	});
	
	$( "#options_accordion" ).accordion({
		autoHeight: false,
		active: option_count-1,
		header: "> div > h3"
	}).sortable({
		axis: "y",
		handle: "h3",
		stop: function() {
			stop = true;
		}
	});
	

	$('.option_item_form').sortable({
		axis: 'y',
		handle: 'span',
		stop: function() {
			stop = true;
		}
	});
	
	
}
function delete_product_option(id)
{
	//remove the option if it exists. this function is also called by the lightbox when an option is deleted
	$('#options-'+id).remove();
}
//]]>
</script>


<?php echo form_open(ADMIN_AREA.'/products/form/'.$id ); ?>
<div class="row">
	<div class="span8">
		<div class="tabbable">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#product_info" data-toggle="tab"><?php echo lang('details');?></a></li>
				<?php //if there aren't any files uploaded don't offer the client the tab
				if (count($file_list) > 0):?>
				<li><a href="#product_downloads" data-toggle="tab"><?php echo lang('digital_content');?></a></li>
				<?php endif;?>
				<li><a href="#product_options" data-toggle="tab"><?php echo lang('options');?></a></li>
				<li><a href="#gc_product_related" data-toggle="tab"><?php echo lang('related_products');?></a></li>
				<li><a href="#gc_product_photos" data-toggle="tab"><?php echo lang('images');?></a></li>
			</ul>
		</div>
		<div class="tab-content">
			<div class="tab-pane active" id="product_info">
				<fieldset>
					<div class="row">
						<div class="span8">
							<?php
							$data	= array('placeholder'=>lang('name'), 'name'=>'name', 'value'=>set_value('name', $name), 'class'=>'span8');
							echo form_input($data);
							?>
						</div>
					</div>
					<div class="row">
						<div class="span8">
							<?php
							$data	= array('id'=>'description', 'name'=>'description', 'class'=>'span8 tinyMCE', 'value'=>set_value('description', $description));
							echo form_textarea($data);
							?>
							<label></label>
							<input class="btn pull-right" type="button" onclick="toggleEditor('description'); return false;" value="Toggle WYSIWYG" />
						</div>
					</div>
					
					<div class="row">
						<div class="span8">
							<label><?php echo lang('excerpt');?></label>
							<?php
							$data	= array('name'=>'excerpt', 'value'=>set_value('excerpt', $excerpt), 'class'=>'span8', 'rows'=>5);
							echo form_textarea($data);
							?>
						</div>
					</div>
					
					<div class="row">
						<div class="span3">
							<label for="track_stock"><?php echo lang('track_stock');?> </label>
							<?php
						 	$options = array(	 '1'	=> lang('yes')
												,'0'	=> lang('no')
												);
							echo form_dropdown('track_stock', $options, set_value('track_stock',$track_stock), 'class="span3"');
							?>
						</div>
						<div class="span3">
							<label for="fixed_quantity"><?php echo lang('fixed_quantity');?> </label>
							<?php
						 	$options = array(	 '0'	=> lang('no')
												,'1'	=> lang('yes')
												);
							echo form_dropdown('fixed_quantity', $options, set_value('fixed_quantity',$fixed_quantity), 'class="span3"');
							?>
						</div>
						<div class="span2">
							<label for="quantity"><?php echo lang('quantity');?> </label>
							<?php
							$data	= array('name'=>'quantity', 'value'=>set_value('quantity', $quantity), 'class'=>'span2');
							echo form_input($data);
							?>
						</div>
						
						



						
						
					</div>
				</fieldset>
			</div>
			
			<div class="tab-pane" id="product_downloads">
				<div class="alert alert-info">
					<?php echo lang('digital_products_desc'); ?>
				</div>
				<fieldset>
					<table class="table table-striped">
						<thead>
							<tr>
								<th><?php echo lang('filename');?></th>
								<th><?php echo lang('title');?></th>
								<th style="width:70px;"><?php echo lang('size');?></th>
								<th style="width:16px;"></th>
							</tr>
						</thead>
						<tbody>
						<?php echo (count($file_list) < 1)?'<tr><td style="text-align:center;" colspan="6">'.lang('no_files').'</td></tr>':''?>
						<?php foreach ($file_list as $file):?>
							<tr>
								<td><?php echo $file->filename ?></td>
								<td><?php echo $file->title ?></td>
								<td><?php echo $file->size ?></td>
								<td><?php echo form_checkbox('downloads[]', $file->id, in_array($file->id, $product_files)); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</fieldset>
			</div>
			
			<div class="tab-pane" id="product_options">
				
				<div class="row">
					<div class="span8" style="text-align:right;">
						<?php echo lang('select_option_type');?>
						<button type="button" class="btn add_option" rel="checklist"><?php echo lang('checklist');?></button>
						<button type="button" class="btn add_option" rel="radiolist"><?php echo lang('radiolist');?></button>
						<button type="button" class="btn add_option" rel="droplist"><?php echo lang('droplist');?></button>
						<button type="button" class="btn add_option" rel="textfield"><?php echo lang('textfield');?></button>
						<button type="button" class="btn add_option" rel="textarea"><?php echo lang('textarea');?></button>
					</div>
				</div>
				
				<div class="row">
					<div class="span8" id="selected_options">
						

						<div id="options_accordion">
						<?php 
							$count	= 0;
							if(!empty($product_options)):
								//print_r($product_options);
								foreach($product_options as $option):
									//print_r($option);
									$option	= (object)$option;

									if(empty($option->required))
									{
										$option->required = false;
									}
								?>
									<div id="option-<?php echo $count;?>">
										<h3><a href="#"><?php echo $option->type.' > '.$option->name; ?> </a></h3>

										<div class="span">
											<?php echo lang('option_name');?>

												<a style="float:right" onclick="remove_option(<?php echo $count ?>)" class="ui-state-default ui-corner-all" ><span class="ui-icon ui-icon-circle-minus"></span></a>

											<input class="input gc_tf2" type="text" name="option[<?php echo $count;?>][name]" value="<?php echo $option->name;?>"/>

											<input type="hidden" name="option[<?php echo $count;?>][type]" value="<?php echo $option->type;?>" />
											<input class="checkbox" type="checkbox" name="option[<?php echo $count;?>][required]" value="1" <?php echo ($option->required)?'checked="checked"':'';?>/> <?php echo lang('required');?>

											<?php if($option->type!='textarea' && $option->type!='textfield') { ?>
											<button id="add_item_<?php echo $count;?>" type="button" rel="<?php echo $option->type;?>"onclick="add_item($(this).attr('rel'), <?php echo $count;?>);"><?php echo lang('add_item');?></button>
											<?php } ?>


											<div class="option_item_form">
											<?php if($option->type!='textarea' && $option->type!='textfield') { ?><ul class="sortable" id="option_items_<?php echo $count;?>"><?php } ?>
											<?php if(!empty($option->values))
														$valcount = 0;
														foreach($option->values as $value) : 
															$value = (object)$value;?>

													<?php if($option->type!='textarea' && $option->type!='textfield') { ?><li id="value-<?php echo $count;?>-<?php echo $valcount;?>"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><?php } ?>
													<div  style="margin:2px"><span><?php echo lang('name');?> </span><input class="req gc_tf2" type="text" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][name]" value="<?php echo $value->name ?>" />

													<span><?php echo lang('value');?> </span><input class="req gc_tf2" type="text" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][value]" value="<?php echo $value->value ?>" />
													<span><?php echo lang('weight');?> </span><input class="req gc_tf2" type="text" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][weight]" value="<?php echo $value->weight ?>" />
													<span><?php echo lang('price');?> </span><input class="req gc_tf2" type="text" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][price]" value="<?php echo $value->price ?>" />
													<?php if($option->type == 'textfield'):?>

													<span><?php echo lang('limit');?> </span><input class="req gc_tf2" type="text" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][limit]" value="<?php echo $value->limit ?>" />

													<?php endif;?>
													<?php if($option->type!='textarea' && $option->type!='textfield') { ?>
													<a onclick="if(confirm('<?php echo lang('confirm_remove_value');?>')) $('#value-<?php echo $count;?>-<?php echo $valcount;?>').remove()" class="ui-state-default ui-corner-all" style="float:right;"><span class="ui-icon ui-icon-circle-minus"></span></a>
													<?php } ?>
													</div>
													<?php if($option->type!='textarea' && $option->type!='textfield') { ?>
													</li>
													<?php } ?>


											<?php	$valcount++;
											 		endforeach;  ?>
											 <?php if($option->type!='textarea' && $option->type!='textfield') { ?></ul><?php } ?>
											</div>


										</div>
									</div>

								<?php 


								$count++; 
								endforeach;
							endif;
							?>

						</div>

						
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span4">
		<?php
	 	$options = array(	 '1'	=> lang('enabled')
							,'0'	=> lang('disabled')
							);
		echo form_dropdown('enabled', $options, set_value('enabled',$enabled), 'class="span4"');
		?>
		
		<?php
		$options = array(	 '1'	=> lang('shippable')
							,'0'	=> lang('not_shippable')
							);
		echo form_dropdown('shippable', $options, set_value('shippable',$shippable), 'class="span4"');
		?>
		
		<?php
		$options = array(	 '1'	=> lang('taxable')
							,'0'	=> lang('not_taxable')
							);
		echo form_dropdown('taxable', $options, set_value('taxable',$taxable), 'class="span4"');
		?>
		
		<label for="slug"><?php echo lang('slug');?> </label>
		<?php
		$data	= array('name'=>'slug', 'value'=>set_value('slug', $slug), 'class'=>'span4');
		echo form_input($data);?>
		
		<label for="sku"><?php echo lang('sku');?></label>
		<?php
		$data	= array('name'=>'sku', 'value'=>set_value('sku', $sku), 'class'=>'span4');
		echo form_input($data);?>
		
		<label for="weight"><?php echo lang('weight');?> </label>
		<?php
		$data	= array('name'=>'weight', 'value'=>set_value('weight', $weight), 'class'=>'span4');
		echo form_input($data);?>
		
		<label for="price"><?php echo lang('price');?></label>
		<?php
		$data	= array('name'=>'price', 'value'=>set_value('price', $price), 'class'=>'span4');
		echo form_input($data);?>
		
		<label for="saleprice"><?php echo lang('saleprice');?></label>
		<?php
		$data	= array('name'=>'saleprice', 'value'=>set_value('saleprice', $saleprice), 'class'=>'span4');
		echo form_input($data);?>
	</div>
</div>

	<div style="border-bottom:50px solid #ccc; "></div>
	

	
	<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>	 
	<div id="gc_product_categories">
		<table class="gc_table" cellspacing="0" cellpadding="0">
		    <thead>
				<tr>
					<th class="gc_cell_left" style="text-align:left"><?php echo lang('name');?></th>
					<th class="gc_cell_right"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				function list_categories($cats, $product_categories, $sub='') {
					
					foreach ($cats as $cat):?>
					<tr class="gc_row">
						<td><?php echo  $sub.$cat['category']->name; ?></td>
						<td style="text-align:right">
							<input type="checkbox" name="categories[]" value="<?php echo $cat['category']->id;?>" <?php echo (in_array($cat['category']->id, $product_categories))?'checked="checked"':'';?>/>
						</td>
					</tr>
					<?php
					if (sizeof($cat['children']) > 0)
					{
						$sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
							$sub2 .=  '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
						list_categories($cat['children'], $product_categories, $sub2);
					}
					endforeach;
				}

				list_categories($categories, $product_categories);
				?>
			</tbody>
		</table>
	</div>

	
	<div id="gc_product_seo">
		<div class="gc_field2">
		<label for="seo_title"><?php echo lang('seo_title');?> </label>
		<?php
		$data	= array('id'=>'seo_title', 'name'=>'seo_title', 'value'=>set_value('seo_title', $seo_title), 'class'=>'gc_tf1');
		echo form_input($data);
		?>
		</div>
		
		<div class="gc_field">
		<label><?php echo lang('meta');?></label> <small><?php echo lang('meta_example');?></small>
		<?php
		$data	= array('id'=>'meta', 'name'=>'meta', 'value'=>set_value('meta', html_entity_decode($meta)), 'class'=>'gc_tf1');
		echo form_textarea($data);
		?>
		</div>
	</div>

	<div id="gc_product_options">
	
		

	</div>
	<div id="gc_product_related">
		<div class="gc_field">
			<label><?php echo lang('select_a_product')?>: </label>
			<select id="product_list">
			<?php foreach($product_list as $p): if(!empty($p) && $id != $p->id):?>
				<option id="product_item_<?php echo $p->id;?>" value="<?php echo $p->id; ?>"><?php echo $p->name;?></option>
			<?php endif; endforeach;?>
			</select>
			
			<a href="#" onclick="add_related_product();return false;" class="button" title="Add Related Product"><?php echo lang('add_related_product');?></a>
		</div>
		<?php 
		
		$products = array();
		foreach($product_list as $p)
		{
			$products[$p->id] = $p->name;
		}
		
		?>
		<table class="gc_table" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="gc_cell_left"><?php echo lang('product_name');?></th>
					<th class="gc_cell_right"></th>
				</tr>
			</thead>
			<tbody id="product_items_container">
			<?php if(!empty($related_products)):foreach($related_products as $rel): if(!empty($rel)) :?>
				<?php 
					if(array_key_exists($rel, $products))
					{
						echo related_items($rel, $products[$rel]);
					}
				?>
			<?php endif; endforeach; endif;?>
			</tbody>
		</table>
	</div>
	<div id="gc_product_photos">
		<div class="gc_segment_content">
			<iframe src="<?php echo site_url(ADMIN_AREA.'/products/product_image_form');?>" style="height:75px; border:0px;">
			</iframe>
			<div id="gc_photos">
			<?php
			
			foreach($images as $photo_id=>$photo_obj)
			{
				if(!empty($photo_obj))
				{
					$photo = (array)$photo_obj;
					add_image($photo_id, $photo['filename'], $photo['alt'], $photo['caption'], isset($photo['primary']));
				}
				
			}
			?>
			</div>
		</div>
	</div>
</div>

<div class="form-actions">
	<button type="submit" class="btn btn-primary"><?php echo lang('form_save');?></button>
</div>
</form>

<?php
function add_image($photo_id, $filename, $alt, $caption, $primary=false)
{	ob_start();
	?>
	<div class="gc_photo" id="gc_photo_<?php echo $photo_id;?>">
		<table cellspacing="0" cellpadding="0">
			<tr>
				<td style="width:81px;padding-right:10px;" rowspan="2">
					<input type="hidden" name="images[<?php echo $photo_id;?>][filename]" value="<?php echo $filename;?>"/>
					<img class="gc_thumbnail" src="<?php echo base_url('uploads/images/thumbnails/'.$filename);?>"/>
				</td>
				<td>
					<input type="radio" name="primary_image" value="<?php echo $photo_id;?>" <?php if($primary) echo 'checked="checked"';?>/> <?php echo lang('primary');?>
					
					<a onclick="return remove_image($(this));" rel="<?php echo $photo_id;?>" class="button" style="float:right; font-size:9px;"><?php echo lang('remove');?></a>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<tr>
							<td><?php echo lang('alt_tag');?></td>
							<td><input name="images[<?php echo $photo_id;?>][alt]" value="<?php echo $alt;?>" class="gc_tf2"/></td>
						</tr>
						<tr>
							<td><?php echo lang('caption');?></td>
							<td><textarea name="images[<?php echo $photo_id;?>][caption]"><?php echo $caption;?></textarea></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<?php
	$stuff = ob_get_contents();

	ob_end_clean();
	
	echo replace_newline($stuff);
}

function add_option($name, $option_id, $type)
{
	ob_start();
	?>
	<div id="option-<?php echo $option_id;?>">
		<h3><a href="#"><?php echo $type.' > '.$name; ?></a></h3>
		<div style="text-align: left">
			<?php echo lang('option_name');?>
			<span style="float:right">
			
			<a onclick="remove_option(<?php echo $option_id ?>)" class="ui-state-default ui-corner-all" style="float:right;"><span class="ui-icon ui-icon-circle-minus"></span></a></span>
			<input class="input gc_tf1" type="text" name="option[<?php echo $option_id;?>][name]" value="<?php echo $name;?>"/>
			<input type="hidden" name="option[<?php echo $option_id;?>][type]" value="<?php echo $type;?>" />
			<input class="checkbox" type="checkbox" name="option[<?php echo $option_id;?>][required]" value="1"/> <?php echo lang('required');?>
			
	
			<button id="add_item_<?php echo $option_id;?>" type="button" rel="<?php echo $type;?>"onclick="add_item($(this).attr(\'rel\'), <?php echo $option_id;?>);"><?php echo lang('add_item');?></button>
		  
			<div class="option_item_form" >
				<ul class="sortable" id="option_items_<?php echo $option_id;?>">
				
				</ul>
			</div>
		</div>
	</div>
	<?php
	$stuff = ob_get_contents();

	ob_end_clean();
	
	echo replace_newline($stuff);
}
//this makes it easy to use the same code for initial generation of the form as well as javascript additions
function replace_newline($string) {
  return (string)str_replace(array("\r", "\r\n", "\n", "\t"), ' ', $string);
}
?>
<script type="text/javascript">
//<![CDATA[
var option_count	= $('#options_accordion>h3').size();
	
var count = <?php echo $count;?>;

function add_related_product()
{

	//if the related product is not already a related product, add it
	if($('#related_product_'+$('#product_list').val()).length == 0 && $('#product_list').val() != null)
	{
		<?php $new_item	 = str_replace(array("\n", "\t", "\r"),'',related_items("'+$('#product_list').val()+'", "'+$('#product_item_'+$('#product_list').val()).html()+'"));?>
		var related_product = '<?php echo $new_item;?>';
		$('#product_items_container').append(related_product);
		$('.list_buttons').buttonset();
	}
	else
	{
		if($('#product_list').val() == null)
		{
			alert('<?php echo lang('alert_select_product');?>');
		}
		else
		{
			alert('<?php echo lang('alert_product_related');?>');
		}
	}
}

function remove_related_product(id)
{
	if(confirm('<?php echo lang('confirm_remove_related');?>?'))
	{
		$('#related_product_'+id).remove();
	}
}

function photos_sortable()
{
	$('#gc_photos').sortable({	
		handle : '.gc_thumbnail',
		items: '.gc_photo',
		axis: 'y',
		scroll: true
	});
}

//]]>
</script>


<?php
function related_items($id, $name) {
	return '
			<tr id="related_product_'.$id.'" class="gc_row">
				<td class="gc_cell_left" >
					<input type="hidden" name="related_products[]" value="'.$id.'"/>
					'.$name.'</td>
				<td class="gc_cell_right list_buttons">
					<a href="#" onclick="remove_related_product('.$id.'); return false;">'.lang('remove').'</a>
				</td>
			</tr>
		';
 } ?>
<?php include('footer.php'); ?>