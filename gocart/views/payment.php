<?php include('header.php'); ?>
<?php if (validation_errors()): ?>
    <div class="gc_reg_error"><?php echo validation_errors(); ?></div>
<?php endif; ?>
<script type="text/javascript">
	
</script>
<div style="margin:15px">
    <table>
        <tr>
            <td>
                <h3>Payment Method</h3>


                <?php foreach ($payment_methods as $method => $info): ?>
                    <?php
                    if ($module == $method) {
                        $selected = 'checked';
                    } else {
                        $selected = '';
                    }
                    ?>
                    <p><input type="radio" name="payment_method" <?php echo $selected; ?> value="<?php echo $method; ?>" onchange="set_payment(this.value)"> <?php echo $info['name']; ?></p>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>
</div>
<?php foreach ($payment_methods as $method => $info): ?>
    <?php echo secure_form_open('checkout/payment'); ?>
    <?php
    if ($module == $method) {
        $selected = ' style="display:block;"';
    } else {
        $selected = '';
    }
    ?>
    <div class="gc_payment_form" id="pmnt_<?php echo $method; ?>"<?php echo $selected; ?>>
        <h3><?php echo $info['name']; ?></h3>
    <?php echo $info['form']; ?>
        <input type="hidden" name="module" value="<? echo $method; ?>"/>
        <input type="submit" name="submit" value="Submit Payment Method" />
    </div>
        <?php echo form_close(); ?>
<?php endforeach; ?>

<script type="text/javascript">

    function set_payment(value) {
	
        $('.gc_payment_form').hide();
        $('#pmnt_'+value).show();

    }

    /*
$(document).ready(function(){
	
        //make this on keypress and on change, so if they change it with arrow keys it will get picked up
        $('#payment_method').change(function(){
                //hide all payment methods
                $('.gc_payment_form').hide();
                if($('#payment_method').val() != 'none')
                {
                        $('#pmnt_'+$(this).val()).show();
                }
        });
	
        $('#payment_method').keypress(function(){
                //hide all payment methods
                $('.gc_payment_form').hide();
                if($('#payment_method').val() != 'none')
                {
                        $('#pmnt_'+$(this).val()).show();
                }
        });
});
     */
</script>
<?php include('footer.php'); ?>
