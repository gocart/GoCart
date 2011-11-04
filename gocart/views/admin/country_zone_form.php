<?php include('header.php'); ?>

<?php echo form_open($this->config->item('admin_folder') . '/locations/zone_form/' . $id); ?>

<div class="button_set">
    <input type="submit" value="Save Zone" />
</div>

<div id="gc_tabs">
    <ul>
        <li><a href="#gc_country_details">Zone Details</a></li>
    </ul>

    <div id="gc_country_details">
        <div class="gc_field2">
            <div class="gc_field2">
                <label for="country_id">Country: </label>
                <?php
                $country_ids = array();
                foreach ($countries as $c) {
                    $country_ids[$c->id] = $c->name;
                }
                ?>
                <?php echo form_dropdown('country_id', $country_ids, set_value('country_id', $country_id), 'class="gc_tf1"'); ?>
            </div>
            <div class="gc_field2">
                <label>Name: </label>
<?php
$data = array('id' => 'name', 'name' => 'name', 'value' => set_value('name', $name), 'class' => 'gc_tf1');
echo form_input($data);
?>
            </div>

            <div class="gc_field2">
                <label for="code">Code: </label>
<?php
$data = array('name' => 'code', 'value' => set_value('code', $code), 'class' => 'gc_tf1');
echo form_input($data);
?>
            </div>
            <div class="gc_field2">
                <label for="max_product_instances">Tax Rate: </label>
<?php
$data = array('name' => 'tax', 'maxlength' => '10', 'value' => set_value('tax', $tax), 'class' => 'gc_tf1');
echo form_input($data);
?>
            </div>
            <div class="gc_field2">
                <?php $status = array('name' => 'status', 'value' => 1, 'checked' => set_checkbox('status', 1, (bool) $status), 'class="gc_tf1"'); ?>
<?php echo form_checkbox($status); ?> <label>Enabled</label>
            </div>
        </div>

    </div>
</form>

<script type="text/javascript">
    $(document).ready(function(){
        $("#gc_tabs").tabs();
    });
</script>


<?php include('footer.php'); ?>
