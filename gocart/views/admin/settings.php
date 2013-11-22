<?php echo form_open_multipart(config_item('admin_folder').'/settings');?>

<fieldset>
    <legend><?php echo lang('shop_details');?></legend>
    <div class="row">
        <div class="span4">
            <label><?php echo lang('company_name');?></label>
            <?php echo form_input(array('class'=>'span4', 'name'=>'company_name', 'value'=>set_value('company_name', $company_name)));?>
        </div>

        <div class="span4">
            <label><?php echo lang('theme');?></label>
            <?php echo form_dropdown('theme', $themes, set_value('theme', $theme), 'class="span4"');?>
        </div>

        <div class="span4">
            <label><?php echo lang('cart_email');?></label>
            <?php echo form_input(array('class'=>'span4', 'name'=>'email', 'value'=>set_value('email', $email)));?>
        </div>        
    </div>
</fieldset>

<fieldset>
    <legend><?php echo lang('ship_from_address');?></legend>

    <label><?php echo lang('country');?></label>
    <?php echo form_dropdown('country_id', $countries_menu, set_value('country_id', $country_id), 'id="country_id" class="span12"');?>

    <div class="row">
        <div class="span6">
            <label><?php echo lang('address1');?></label>
            <?php echo form_input(array('name'=>'address1', 'class'=>'span12','value'=>set_value('address1',$address1)));?>
        </div>
    </div>

    <div class="row">
        <div class="span6">
            <?php echo form_input(array('name'=>'address2', 'class'=>'span12','value'=> set_value('address2',$address2)));?>
        </div>
    </div>

    <div class="row">
        <div class="span4">
            <label><?php echo lang('city');?></label>
            <?php echo form_input(array('name'=>'city','class'=>'span4', 'value'=>set_value('city',$city)));?>
        </div>
        <div class="span6">
            <label><?php echo lang('state');?></label>
            <?php echo form_dropdown('zone_id', $zones_menu, set_value('zone_id', $zone_id), 'id="zone_id" class="span6"');?>
        </div>
        <div class="span2">
            <label><?php echo lang('zip');?></label>
            <?php echo form_input(array('maxlength'=>'10', 'class'=>'span2', 'name'=>'zip', 'value'=> set_value('zip',$zip)));?>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo lang('locale_currency');?></legend>

    <div class="row">
        <div class="span6">
            <label><?php echo lang('locale');?></label>
            <?php echo form_dropdown('locale', $locales, set_value('locale', $locale), 'class="span6"');?>
        </div>
        <div class="span6">
            <label><?php echo lang('currency');?></label>
            <?php echo form_dropdown('currency_iso', $iso_4217, set_value('currency_iso', $currency_iso), 'class="span6"');?>
        </div>
        
    </div>

</fieldset>

<fieldset>
    <legend><?php echo lang('security');?></legend>

    <label><?php echo lang('admin_folder');?></label>
    <?php echo form_input(array('name'=>'admin_folder', 'class'=>'span12','value'=>set_value('admin_folder',$admin_folder)));?>

    <label class="checkbox">
        <?php echo form_checkbox('ssl_support', '1', set_value('ssl_support',$ssl_support));?> <?php echo lang('ssl_support');?>
    </label>

     <label class="checkbox">
        <?php echo form_checkbox('require_login', '1', set_value('require_login',$require_login));?> <?php echo lang('require_login');?>
     </label>

    <label class="checkbox">
        <?php echo form_checkbox('new_customer_status', '1', set_value('new_customer_status',$new_customer_status));?> <?php echo lang('new_customer_status');?>
    </label>

</fieldset>

<fieldset>
    <legend><?php echo lang('package_details');?></legend>

    <div class="row">
        <div class="span3">
            <label><?php echo lang('weight_unit');?></label>
            <?php echo form_input(array('name'=>'weight_unit', 'class'=>'span3','value'=>set_value('weight_unit',$weight_unit)));?>    
        </div>
        <div class="span3">
            <label><?php echo lang('dimension_unit');?></label>
            <?php echo form_input(array('name'=>'dimension_unit', 'class'=>'span3','value'=>set_value('dimension_unit',$dimension_unit)));?>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo lang('order_inventory');?></legend>

    <table class="table">
        <thead>
            <tr>
                <th><?php echo lang('order_status');?></th>
                <th><?php echo lang('order_statuses');?></th>
                <th style="text-align:right;">
                    <input type="text" value="" id="new_order_status_field" style="margin:0px;" placeholder="<?php echo lang('status_name');?>"/>
                    <button type="button" class="btn" onclick="add_status()"><i class="icon-plus"></i></button>
                </th>
            </tr>
        </thead>
        <tbody id="order_statuses">
        <?php
        $statuses = json_decode($order_statuses, true);
        
        if(!is_array($statuses))
        {
            $statuses = array();
        }
        foreach($statuses as $os):?>
            <tr>
                <td><input type="radio" value="<?php echo htmlentities($os);?>" name="order_status" <?php echo set_radio('order_status', $os, ($os == $order_status)?true:false)?>></td>
                <td><?php echo htmlentities($os);?></td>
                <td style="text-align:right;">
                    <button type="button" onclick="if(confirm('<?php echo lang('confirm_delete_order_status');?>')){ delete_status($(this).parent().siblings().first().html()); $(this).parent().parent().remove();}" class="btn btn-danger">
                        <i class="icon-remove icon-white"></i>
                    </button>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
    <?php echo form_textarea(array('name'=>'order_statuses', 'value'=>set_value('order_statuses',$order_statuses), 'id'=>'order_statuses_json'));?>   

    <label class="checkbox">
        <?php echo form_checkbox('inventory_enabled', '1', set_value('inventory_enabled',$inventory_enabled));?> <?php echo lang('inventory_enabled');?>
    </label>

    <label class="checkbox">
        <?php echo form_checkbox('allow_os_purchase', '1', set_value('allow_os_purchase',$allow_os_purchase));?> <?php echo lang('allow_os_purchase');?>
    </label>

</fieldset>

<fieldset>
    <legend><?php echo lang('tax_settings');?></legend>

    <label><?php echo lang('tax_address');?></label>
    <?php $address_options = array('ship'=>lang('shipping_address'), 'bill'=>lang('billing_address'));?>
    <?php echo form_dropdown('tax_address', $address_options, set_value('tax_address',$tax_address));?>
    
    <label class="checkbox">
        <?php echo form_checkbox('tax_shipping', '1', set_value('tax_shipping',$tax_shipping));?> <?php echo lang('tax_shipping');?>
    </label>

</fieldset>


<input type="submit" class="btn btn-primary" value="<?php echo lang('save');?>" />

</form>


<script>
    var order_statuses = <?php echo $order_statuses;?>;
    
    function add_status()
    {
        var status = $('#new_order_status_field').val();

     
            order_statuses[htmlEntities(status)] = htmlEntities(status);

            var os_submission = JSON.stringify(order_statuses);
            $('#order_statuses_json').val(os_submission);
            
            var row = '<tr><td><input type="radio" value="'+htmlEntities(status)+'" name="order_status"></td><td>'+htmlEntities(status)+'</td><td style="text-align:right;"><button type="button" onclick="if(confirm(\'<?php echo lang('confirm_delete_order_status');?>\')){delete_status($(this).parent().siblings().first().html()); $(this).parent().parent().remove();}" class="btn btn-danger"><i class="icon-remove icon-white"></i></button></td></tr>';
            $('#order_statuses').append(row);

        $('#new_order_status_field').val('')
        
    }

    function delete_status(status)
    {
        order_statuses[status] = undefined;
        var os_submission = JSON.stringify(order_statuses);
        $('#order_statuses_json').val(os_submission);
    }

    $(document).ready(function(){
        $('#country_id').change(function(){
            $.post('<?php echo site_url(config_item('admin_folder').'/locations/get_zone_menu');?>',{id:$('#country_id').val()}, function(data) {
              $('#zone_id').html(data);
            });
        });
    });
    
    function htmlEntities(str) {
       return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }      
</script>
<style type="text/css">
#order_statuses_json {
   display:none;
}
</style>
