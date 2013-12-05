<?php if(!empty($category->description)): ?>
<div class="row">
    <div class="span12"><?php echo $category->description; ?></div>
</div>
<?php endif; ?>
        
<div class="row">
    
    <?php
    $cols = 4;
    if(isset($category)):?>
        <?php if(isset($this->categories[$category->id] ) && count($this->categories[$category->id]) > 0): $cols=3; ?>
            <div class="span3">
                <ul class="nav nav-list well">
                    <li class="nav-header">
                    <?php echo lang('subcategories'); ?>
                    </li>
            
                    <?php foreach($this->categories[$category->id] as $subcategory):?>
                        <li><a href="<?php echo site_url(implode('/', $base_url).'/'.$subcategory->slug); ?>"><?php echo $subcategory->name;?></a></li>
                    <?php endforeach;?>
                </ul>
            </div>
        
            <div class="span9">
        <?php endif;?>
    <?php endif;?>
    
    <?php if($cols == 4):?>
        <div class="span12">
    <?php endif;?>
    
    <?php if(count($products) == 0):?>
        <h2 style="margin:50px 0px; text-align:center;">
            <?php echo lang('no_products');?>
        </h2>
    <?php elseif(count($products) > 0):?>
        
        <div class="row" style="margin-top:20px; margin-bottom:15px">
            <div class="<?php echo ($cols == 4)?'span9':'span6';?>">
                <?php echo $this->pagination->create_links();?>&nbsp;
            </div>
            <div class="span3 pull-right">
                <select class="span3" id="sort_products" onchange="window.location='<?php echo site_url(uri_string());?>/'+$(this).val();">
                    <option value=''><?php echo lang('default');?></option>
                    <option<?php echo(!empty($_GET['by']) && $_GET['by']=='name/asc')?' selected="selected"':'';?> value="?by=name/asc"><?php echo lang('sort_by_name_asc');?></option>
                    <option<?php echo(!empty($_GET['by']) && $_GET['by']=='name/desc')?' selected="selected"':'';?>  value="?by=name/desc"><?php echo lang('sort_by_name_desc');?></option>
                    <option<?php echo(!empty($_GET['by']) && $_GET['by']=='price/asc')?' selected="selected"':'';?>  value="?by=price/asc"><?php echo lang('sort_by_price_asc');?></option>
                    <option<?php echo(!empty($_GET['by']) && $_GET['by']=='price/desc')?' selected="selected"':'';?>  value="?by=price/desc"><?php echo lang('sort_by_price_desc');?></option>
                </select>
            </div>
        </div>

        <div class="category_container">
            <?php
            
            $itm_cnt = 1;
            foreach($products as $product):
                if($itm_cnt == 1):?>
                    <div class="row">
                <?php endif;?>

                <div class="span3 product">
                    <div>
                        <?php
                        $photo  = theme_img('no_picture.png', lang('no_image_available'));
                        $product->images    = array_values($product->images);
            
                        if(!empty($product->images[0]))
                        {
                            $primary    = $product->images[0];
                            foreach($product->images as $photo)
                            {
                                if(isset($photo->primary))
                                {
                                    $primary    = $photo;
                                }
                            }

                            $photo  = '<img src="'.base_url('uploads/images/thumbnails/'.$primary->filename).'" alt="'.$product->seo_title.'"/>';
                        }
                        ?>
                        <div class="product-image">
                            <a class="thumbnail" href="<?php echo site_url(implode('/', $base_url).'/'.$product->slug); ?>">
                                <?php echo $photo; ?>
                            </a>
                        </div>
                        <h5 style="margin-top:5px;">
                            <a href="<?php echo site_url(implode('/', $base_url).'/'.$product->slug); ?>"><?php echo $product->name;?></a>
                            <?php if($this->session->userdata('admin')): ?>
                                <a class="btn" title="<?php echo lang('edit_product'); ?>" href="<?php echo  site_url($this->config->item('admin_folder').'/products/form/'.$product->id); ?>"><i class="icon-pencil"></i></a>
                            <?php endif; ?>
                        </h5>

                        <?php if($product->excerpt != ''): ?>
                            <div class="excerpt"><?php echo $product->excerpt; ?></div>
                        <?php endif; ?>
                    
                        <div>
                            <?php if($product->saleprice > 0):?>
                                <span class="price-slash"><?php echo lang('product_reg');?> <?php echo format_currency($product->price); ?></span>
                                <span class="price-sale"><?php echo lang('product_sale');?> <?php echo format_currency($product->saleprice); ?></span>
                            <?php else: ?>
                                <span class="price-reg"><?php echo lang('product_price');?> <?php echo format_currency($product->price); ?></span>
                            <?php endif; ?>
                        </div>
                    
                        <?php if((bool)$product->track_stock && $product->quantity < 1 && config_item('inventory_enabled')) { ?>
                            <div class="stock_msg"><?php echo lang('out_of_stock');?></div>
                        <?php } ?>
                    </div>
                </div>
                <?php if($itm_cnt == $cols) {
                    $itm_cnt = 1;
                    echo '</div>';
                }
                else
                {
                    $itm_cnt++;
                }?>
            <?php endforeach; ?>
            <?php if($itm_cnt != 1){
                echo '</div>';
            }?>
        </div>
    <?php endif;?>
    </div>
</div>