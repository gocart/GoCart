<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder') . '/giftcards/form/'); ?>
<div class="button_set">
    <input type="submit" value="Save Gift Card"/>
</div>

<div id="gc_tabs">
    <ul>
        <li><a href="#gc_coupon_attributes">Attributes</a></li>
    </ul>

    <div id="gc_coupon_attributes">
        <div class="gc_field2">
            <label>To (Name): </label>
            <?php
            $data = array('id' => 'to_name', 'name' => 'to_name', 'value' => set_value('code'), 'class' => 'gc_tf1');
            echo form_input($data);
            ?>
        </div>
        <div class="gc_field2">
            <label for="max_uses">To (Email)</label>
            <?php
            $data = array('id' => 'to_email', 'name' => 'to_email', 'value' => set_value('to_email'), 'class' => 'gc_tf1');
            echo form_input($data);
            ?>
        </div>
        <div class="gc_field2">
            <label for="max_uses">Send Email Notification?</label>
            <?php
            $data = array('name' => 'send_notification', 'value' => 'true', 'class' => 'gc_tf1');
            echo form_checkbox($data);
            ?>
        </div>
        <div class="gc_field2">
            <label for="max_uses">From</label>
            <?php
            $data = array('id' => 'from', 'name' => 'from', 'value' => set_value('from'), 'class' => 'gc_tf1');
            echo form_input($data);
            ?>
        </div>
        <div class="gc_field2">
            <label for="max_product_instances">Personal Message: </label>
            <?php
            $data = array('name' => 'personal_message', 'value' => set_value('personal_message'), 'class' => 'gc_tf1');
            echo form_textarea($data);
            ?>
        </div>

        <div class="gc_field2" id="gc_coupon_price_fields">
            <label for="reduction_amount">Amount: </label>
            <?php
            $data = array('id' => 'beginning_amount', 'name' => 'beginning_amount', 'value' => set_value('beginning_amount'), 'class' => 'gc_tf1');
            echo form_input($data);
            ?>
        </div>
    </div>
</div>

</form>

<?php include('footer.php'); ?>
