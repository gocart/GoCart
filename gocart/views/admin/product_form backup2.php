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
	
	$( "#add_option" ).click(function(){
		if($('#option_options').val() != '')
		{
			add_option($('#option_options').val());
			$('#option_options').val('');
		}
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

	$('#options_accordion').collapse();
	
	add_item(type, option_count);
}

function add_item(type, id)
{
	
	var count = $('#option_items_'+id+'>li').size()+1;
	
	append_html = '<tr id="value-'+id+'-'+count+'">';
	
	if(type!='textfield' && type != 'textarea')
	{
		append_html = append_html + '<td><button type="button" class="btn btn-mini"><i class="icon-align-justify"></i></button></td>';
		

		//class="ui-icon ui-icon-arrowthick-2-n-s"></span><a  class="ui-state-default ui-corner-all" style="float:right;"><span class="ui-icon ui-icon-circle-minus"></span></a>';
	}
	
	append_html += '<td><input class="span2" type="text" name="option['+id+'][values]['+count+'][name]" value="" /> </td>'+
	'<td><input class="span1" type="text" name="option['+id+'][values]['+count+'][value]" value="" /></td> '+
	'<td><input class="span1" type="text" name="option['+id+'][values]['+count+'][weight]" value="" /></td> '+
	'<td><input class="span1" type="text" name="option['+id+'][values]['+count+'][price]" value="" /></td>';
	
	if(type == 'textfield')
	{
		append_html += '<td><input class="span1" type="text" name="option['+id+'][values]['+count+'][limit]" value="" /></td>';
	}
	
	if(type!='textfield' && type != 'textarea')
	{
		append_html += '<td><button type="button" class="btn btn-mini btn-danger" onclick="if(confirm(\'<?php echo lang('confirm_remove_value');?>\')) $(\'#value-'+id+'-'+count+'\').remove()"><i class="icon-trash icon-white"></i></button></td>';
	}
	
	append_html += '</tr>';
	
	
	$('#option_items_'+id).append(append_html);	
	
	$(".sortable").sortable();
	
}

function remove_option(id)
{
	if(confirm('<?php echo lang('confirm_remove_option');?>'))
	{
		$('#option-'+id).remove();
		
		option_count --;
		
	}
}

function set_accordion(){
	
	var stop = false;
		
	$( "#options_accordion" ).sortable({
		axis: "y",
		handle: ".handle",
		stop: function() {
			stop = true;
		}
	});
	

	$('.option_item_form').sortable({
		axis: 'y',
		handle: '.handle'
	});
	
	
}
function delete_product_option(id)
{
	//remove the option if it exists. this function is also called by the lightbox when an option is deleted
	$('#options-'+id).remove();
}
//]]>
</script>


<?php echo form_open($this->config->item('admin_folder').'/products/form/'.$id ); ?>
<div class="row">
	<div class="span8">
		<div class="tabbable">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#product_info" data-toggle="tab"><?php echo lang('details');?></a></li>
				<?php //if there aren't any files uploaded don't offer the client the tab
				if (count($file_list) > 0):?>
				<li><a href="#product_downloads" data-toggle="tab"><?php echo lang('digital_content');?></a></li>
				<?php endif;?>
				<li><a href="#product_categories" data-toggle="tab"><?php echo lang('categories');?></a></li>
				<li><a href="#product_options" data-toggle="tab"><?php echo lang('options');?></a></li>
				<li><a href="#product_related" data-toggle="tab"><?php echo lang('related_products');?></a></li>
				<li><a href="#product_photos" data-toggle="tab"><?php echo lang('images');?></a></li>
			</ul>
		</div>
		<div class="tab-content">
			<div class="tab-pane active" id="product_info">
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
					<div class="span8">
						<fieldset>
							<legend>Inventory</legend>
							<div class="row" style="padding-top:10px;">
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
				</div>
				<div class="row">
					<div class="span8">
						<fieldset>
							<legend><?php echo lang('header_information');?></legend>
							<div class="row" style="padding-top:10px;">
								<div class="span8">
									
									<label for="slug"><?php echo lang('slug');?> </label>
									<?php
									$data	= array('name'=>'slug', 'value'=>set_value('slug', $slug), 'class'=>'span8');
									echo form_input($data);?>
									
									<label for="seo_title"><?php echo lang('seo_title');?> </label>
									<?php
									$data	= array('name'=>'seo_title', 'value'=>set_value('seo_title', $seo_title), 'class'=>'span8');
									echo form_input($data);
									?>

									<label for="meta"><?php echo lang('meta');?> <i><?php echo lang('meta_example');?></i></label> 
									<?php
									$data	= array('name'=>'meta', 'value'=>set_value('meta', html_entity_decode($meta)), 'class'=>'span8');
									echo form_textarea($data);
									?>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
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
			
			<div class="tab-pane" id="product_categories">
				<div class="row">
					<div class="span8">
						<table class="table">
							<tbody>
								<?php
								function list_categories($cats, $product_categories, $sub='') {

									foreach ($cats as $cat):?>
									<tr>
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
				</div>
			</div>
			
			<div class="tab-pane" id="product_options">
				<div class="row">
					<div class="span8" id="selected_options">
						<div class="accordion" id="options_accordion">
						<?php 
							$count	= 0;
							if(!empty($product_options)):
								foreach($product_options as $option):
									$option	= (object)$option;
									if(empty($option->required))
									{
										$option->required = false;
									}
									?>
									
									<div class="accordion-group" id="option-<?php echo $count;?>">
										<div class="accordion-heading">
											<button type="button" class="handle btn btn-mini" style="float:left; margin:7px 7px 0px 7px;"><i class="icon-align-justify"></i></button>
											<button class="btn btn-mini btn-danger pull-right" style="margin:7px 7px 0px 0px;" onclick="remove_option(<?php echo $count ?>);"><i class="icon-trash icon-white"></i></button>
											<a class="accordion-toggle" data-parent="#options_accordion" data-toggle="collapse" href="#panel-<?php echo $count;?>"><?php echo $option->type;?> <?php echo (!empty($option->name))?' : '.$option->name:'';?></a>
										</div>
										<div class="accordion-body collapse" id="panel-<?php echo $count;?>">
											<div class="accordion-inner">
												<input type="hidden" name="option[<?php echo $count;?>][type]" value="<?php echo $option->type;?>" />
												<div class="row-fluid">
													<div class="span10">
														<input type="text" class="span10" placeholder="<?php echo lang('option_name');?>" name="option[<?php echo $count;?>][name]" value="<?php echo $option->name;?>"/>
													</div>
													<div class="span2" >
														<input class="checkbox" type="checkbox" name="option[<?php echo $count;?>][required]" value="1" <?php echo ($option->required)?'checked="checked"':'';?>/> <?php echo lang('required');?>
													</div>
												</div>
												<?php if($option->type!='textarea' && $option->type!='textfield'):?>
													<div class="row-fluid">
													<div class="span12">
														<a class="btn" id="add_item_<?php echo $count;?>" type="button" rel="<?php echo $option->type;?>" onclick="add_item($(this).attr('rel'), <?php echo $count;?>);"><?php echo lang('add_item');?></a>
													</div>
												</div>
												<table class="table table-striped">
													<thead>
														<tr>
															<th></th>
															<th><?php echo lang('name');?></th>
															<th><?php echo lang('value');?></th>
															<th><?php echo lang('weight');?></th>
															<th><?php echo lang('price');?></th>
															<th></th>
														</tr>
													</thead>
													<tbody id="option_items_<?php echo $count;?>" class="option_item_form sortable">
														<?php
														if(!empty($option->values)):
															$valcount = 0;
															foreach($option->values as $value) : 
																$value = (object)$value;?>
														<tr>
															<td>
																<button type="button" class="handle btn btn-mini" style="float:left;"><i class="icon-align-justify"></i></button>
															</td>
															<td>
																<input type="text" class="span2" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][name]" value="<?php echo $value->name ?>" />
															</td>
															<td>
																<input type="text" class="span1" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][value]" value="<?php echo $value->value ?>" />
															</td>
															<td>
																<input type="text" class="span1" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][weight]" value="<?php echo $value->weight ?>" />
															</td>
															<td><input  class="span1" type="text" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][price]" value="<?php echo $value->price ?>" /></td>
															<td><a onclick="if(confirm('<?php echo lang('confirm_remove_value');?>')) $('#value-<?php echo $count;?>-<?php echo $valcount;?>').remove()" class="btn btn-mini btn-danger" ><i class="icon-trash icon-white"></i></a></td>
														</tr>
														<?php endforeach;
														endif;
														?>
													</tbody>
												</table>
											<?php else: ?>
												<table class="table table-striped">
													<thead>
														<tr>
															<th><?php echo lang('name');?></th>
															<th><?php echo lang('value');?></th>
															<th><?php echo lang('weight');?></th>
															<th><?php echo lang('price');?></th>
															<?php if($option->type == 'textfield'):?><th><?php echo lang('limit');?></th><?php endif;?>
														</tr>
													</thead>
													<tbody id="option_items_<?php echo $count;?>"  class="option_item_form">
														<?php
														if(!empty($option->values)):
															$valcount = 0;
															foreach($option->values as $value) : 
																$value = (object)$value;?>
														<tr>
															<td>
																<button type="button" class="handle btn btn-mini" style="float:left;"><i class="icon-align-justify"></i></button>
															</td>
															<td>
																<input type="text" class="span2" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][name]" value="<?php echo $value->name ?>" />
															</td>
															<td>
																<input type="text" class="span1" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][value]" value="<?php echo $value->value ?>" />
															</td>
															<td>
																<input type="text" class="span1" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][weight]" value="<?php echo $value->weight ?>" />
															</td>
															<td>
																<input  class="span1" type="text" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][price]" value="<?php echo $value->price ?>" />
															</td>
															<?php if($option->type == 'textfield'):?>
															<td>
																<input class="span1" type="text" name="option[<?php echo $count;?>][values][<?php echo $valcount ?>][limit]" value="<?php echo $value->limit ?>" />
															</td>
															<?php endif;?>
														</tr>
														<?php endforeach;
														endif;
														?>
													</tbody>
												</table>
												<?php endif;?>
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
				
				<div class="row">
					<div class="span8">
						<div style="padding:0px 0px 10px 0px; text-align:center;">
							<select id="option_options" style="margin:0px;">
								<option value=""><?php echo lang('select_option_type')?></option>
								<option value="checklist"><?php echo lang('checklist');?></option>
								<option value="radiolist"><?php echo lang('radiolist');?></option>
								<option value="droplist"><?php echo lang('droplist');?></option>
								<option value="textfield"><?php echo lang('textfield');?></option>
								<option value="textarea"><?php echo lang('textarea');?></option>
							</select>
							<input id="add_option" class="btn" type="button" value="<?php echo lang('add_option');?>" style="margin:0px;"/>
						</div>
					</div>
				</div>
			</div>
			
			<div class="tab-pane" id="product_related">
				<div class="row">
					<div class="span8">
						<label><strong><?php echo lang('select_a_product')?></strong></label>
						<select id="product_list" style="margin:0px;">
							<?php foreach($product_list as $p): if(!empty($p) && $id != $p->id):?>
								<option id="product_item_<?php echo $p->id;?>" value="<?php echo $p->id; ?>"><?php echo $p->name;?></option>
							<?php endif; endforeach;?>
						</select>

						<a href="#" onclick="add_related_product();return false;" class="btn" title="Add Related Product"><?php echo lang('add_related_product');?></a>
					</div>
				</div>

				<?php 

				$products = array();
				foreach($product_list as $p)
				{
					$products[$p->id] = $p->name;
				}

				?>
				<table class="table table-striped" style="margin-top:10px;">
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
			
			<div class="tab-pane" id="product_photos">
				<div class="row">
					<div class="span8">
						<div class="gc_segment_content">
							<iframe src="<?php echo site_url($this->config->item('admin_folder').'/products/product_image_form');?>" style="height:75px; border:0px;">
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
			</div>
		</div>
	</div>
	<div class="span4">
		<?php
	 	$options = array(	 '0'	=> lang('disabled')
							,'1'	=> lang('enabled')
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

<div class="form-actions">
	<button type="submit" class="btn btn-primary"><?php echo lang('form_save');?></button>
</div>
</form>

<?php
function add_image($photo_id, $filename, $alt, $caption, $primary=false)
{	ob_start();
	?>
	<div class="row gc_photo" id="gc_photo_<?php echo $photo_id;?>" style="background-color:#fff; border-bottom:1px solid #ddd; padding-bottom:20px; margin-bottom:20px;">
		<div class="span2">
			<input type="hidden" name="images[<?php echo $photo_id;?>][filename]" value="<?php echo $filename;?>"/>
			<img class="gc_thumbnail" src="<?php echo base_url('uploads/images/thumbnails/'.$filename);?>" style="padding:5px; border:1px solid #ddd"/>
		</div>
		<div class="span6">
			<div class="row">
				<div class="span2">
					<input name="images[<?php echo $photo_id;?>][alt]" value="<?php echo $alt;?>" class="span2" placeholder="<?php echo lang('alt_tag');?>"/>
				</div>
				<div class="span2">
					<input type="radio" name="primary_image" value="<?php echo $photo_id;?>" <?php if($primary) echo 'checked="checked"';?>/> <?php echo lang('primary');?>
				</div>
				<div class="span2">
					<a onclick="return remove_image($(this));" rel="<?php echo $photo_id;?>" class="btn btn-danger" style="float:right; font-size:9px;"><i class="icon-trash icon-white"></i> <?php echo lang('remove');?></a>
				</div>
			</div>
			<div class="row">
				<div class="span6">
					<label><?php echo lang('caption');?></label>
					<textarea name="images[<?php echo $photo_id;?>][caption]" class="span6" rows="3"><?php echo $caption;?></textarea>
				</div>
			</div>
		</div>
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
	
	<div class="accordion-group" id="option-<?php echo $option_id;?>">
		<div class="accordion-heading">
			<button type="button" class="handle btn btn-mini" style="float:left; margin:7px 7px 0px 7px;"><i class="icon-align-justify"></i></button>
			<button class="btn btn-mini btn-danger pull-right" style="margin:7px 7px 0px 0px;" onclick="remove_option(<?php echo $option_id ?>);"><i class="icon-trash icon-white"></i></button>
			<a class="accordion-toggle" data-parent="#options_accordion" data-toggle="collapse" href="#panel-<?php echo $option_id;?>"><?php echo $type;?> <?php echo (!empty($name))?' : '.$name:'';?></a>
		</div>
		<div class="accordion-body" id="panel-<?php echo $option_id;?>">
			<div class="accordion-inner">
				<input type="hidden" name="option[<?php echo $option_id;?>][type]" value="<?php echo $type;?>" />
				<div class="row-fluid">
					<div class="span10">
						<input type="text" class="span10" placeholder="<?php echo lang('option_name');?>" name="option[<?php echo $option_id;?>][name]"/>
					</div>
					<div class="span2">
						<input class="checkbox" type="checkbox" name="option[<?php echo $option_id;?>][required]" value="1" /> <?php echo lang('required');?>
					</div>
				</div>
			<?php if($type!='textarea' && $type!='textfield'):?>
				<div class="row-fluid">
					<div class="span12">
						<a class="btn" id="add_item_<?php echo $option_id;?>" type="button" rel="<?php echo $type;?>" onclick="add_item($(this).attr(\'rel\'), <?php echo $option_id;?>);" style="float:right;"><?php echo lang('add_item');?></a>
					</div>
				</div>
				<table class="table table-striped">
					<thead>
						<tr>
							<th></th>
							<th><?php echo lang('name');?></th>
							<th><?php echo lang('value');?></th>
							<th><?php echo lang('weight');?></th>
							<th><?php echo lang('price');?></th>
							<th></th>
						</tr>
					</thead>
					<tbody id="option_items_<?php echo $option_id;?>" class="option_item_form sortable">
					
					</tbody>
				</table>
			<?php else: ?>
				<table class="table table-striped">
					<thead>
						<tr>
							<th><?php echo lang('name');?></th>
							<th><?php echo lang('value');?></th>
							<th><?php echo lang('weight');?></th>
							<th><?php echo lang('price');?></th>
							<?php if($type == 'textfield'):?><th><?php echo lang('limit');?></th><?php endif;?>
						</tr>
					</thead>
					<tbody id="option_items_<?php echo $option_id;?>"  class="option_item_form">
						
					</tbody>
				</table>
			<?php endif;?>
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
  return trim((string)str_replace(array("\r", "\r\n", "\n", "\t"), ' ', $string));
}
?>
<script type="text/javascript">
//<![CDATA[
var option_count	= $('#options_accordion>.accordion-group').size();
	
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
	if(confirm('<?php echo lang('confirm_remove_related');?>'))
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
				<td>
					<input type="hidden" name="related_products[]" value="'.$id.'"/>
					'.$name.'</td>
				<td>
					<a class="btn btn-danger pull-right" href="#" onclick="remove_related_product('.$id.'); return false;"><i class="icon-trash icon-white"></i> '.lang('remove').'</a>
				</td>
			</tr>
		';
 } ?>
<?php include('footer.php'); ?>