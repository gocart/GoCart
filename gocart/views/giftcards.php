<?php include('header.php'); ?>


<div id="social_sharing">
	<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style ">
	<a class="addthis_button_preferred_1"></a>
	<a class="addthis_button_preferred_2"></a>
	<a class="addthis_button_preferred_3"></a>
	<a class="addthis_button_preferred_4"></a>
	<a class="addthis_button_compact"></a>
	<a class="addthis_counter addthis_bubble_style"></a>
	</div>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e4ed7263599fdd0"></script>
	<!-- AddThis Button END -->
</div>

<div id="product_left">
	<div id="product_image">
		<a href="<?php echo base_url('images/giftcard.gif');?>" rel="gallery" title=""><img src="<?php echo base_url('images/giftcard.gif');?>" alt="Giftcard"/></a>
	</div>
</div>
<div id="product_right">	
	<div class="product_section">
	<?php echo form_open('cart/giftcard');?>
	<table cellpadding="0" cellspacing="5" id="gc_product_form">
		<tr>
			<td colspan="2">
            <?php if(is_array($preset_values)) : 
					
					if(set_value('amount')=='preset_amount') $checked = true;
					else $checked = false;
					
					echo form_radio('amount', 'preset_amount', $checked);
			?>
            
			  <?php echo lang('giftcard_choose_amount');?>
              
              <?php foreach($preset_values as $value)
			  			$options[$value] = "\$$value";
						
					echo form_dropdown('preset_amount', $options, set_value('preset_amount'));
			  ?>
              </td>
              
              </tr>
              <tr>
              <td colspan="2">
           <div> <?php 
				endif;
				if($allow_custom_amount) :
					
					if(set_value('amount')=='custom_amount') $checked = true;
					else $checked = false;
					
					echo form_radio('amount', 'custom_amount', $checked);
				 ?>
			    
			      <?php echo lang('giftcard_custom_amount');?> 
			        <?php echo form_input('custom_amount', set_value('custom_amount')); ?>                    </div>
            <?php endif; ?>                </td>
	  </tr>
		<tr>
		  <td colspan="2">&nbsp;</td>
	  </tr>
		<tr>
		  <td width="99"><?php echo lang('giftcard_to');?></td>
	      <td width="170"><?php echo form_input('gc_to_name', set_value('gc_to_name')); ?></td>
	  </tr>
		<tr>
		  <td><?php echo lang('giftcard_email');?></td>
		  <td><?php echo form_input('gc_to_email', set_value('gc_to_email')); ?></td>
	  </tr>
		<tr>
		  <td><?php echo lang('giftcard_from');?> </td>
	      <td><?php echo form_input('gc_from', set_value('gc_from')) ?></td>
	  </tr>
		<tr>
		  <td><?php echo lang('giftcard_message');?></td>
		  <td><label>
          	<?php 
			$data = array(
              'name'        => 'message',
              'rows'   => '5',
              'cols'        => '30'
            );
			
			echo form_textarea($data,set_value('message')); ?>
		    
		  </label></td>
	  </tr>
		<tr>
		  <td>&nbsp;</td>
		  <td><input type="submit" value="<?php echo lang('form_add_to_cart');?>"/></td>
	  </tr>
	</table>
	
  </form>
</div>
</div>
<?php include('footer.php'); ?>