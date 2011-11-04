<?php include('header.php'); ?>
<?php if (validation_errors()): ?>
    <div class="gc_reg_error"><?php echo validation_errors(); ?></div>
<?php endif; ?>
<?php echo secure_form_open('checkout/shipping'); ?>
<table class="gc_view_cart" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>Item #</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Quantity</th>
            <th style="width:100px;">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $td = 'gc_even';
        foreach ($this->go_cart->contents() as $cartkey => $product) {
            echo '<tr class="' . $td . '"><td>' . $product['code'] . '</td>' .
            '<td>' . $product['name'] . '</td>' .
            '<td>' . $product['excerpt'] . '<br/>';
            if (isset($product['options'])) {
                foreach ($product['options'] as $name => $value) {
                    if (is_array($value)) {
                        echo "<div>$name:<br/>";
                        foreach ($value as $item)
                            echo "- $item<br/>";
                        echo "</div>";
                    } else {
                        echo '<div>' . $name . ': ' . $value . '</div>';
                    }
                }
            }
            echo '</td>';

            echo '<td>$' . number_format($product['price'], 2, '.', ',') . '</td>' .
            '<td style="text-align:center;">' . $product['quantity'] . '</td>' .
            '<td>$' . number_format($product['price'] * $product['quantity'], 2, '.', ',') . '</td></tr>';

            if ($td == 'gc_even') {
                $td = '';
            } else {
                $td = 'gc_even';
            }
        }
        ?>
        <tr><td colspan="6" style="height:3px;overflow:hidden; background-color:#aaaaaa;"></td></tr>
        <tr><td colspan="2" style="background-color:#eeeeee;"><strong>Shipping</strong></td>
            <td colspan="3" id="gc_shipping_method" style="background-color:#eeeeee;">
                <table>
        <?php
        foreach ($shipping_methods as $shipping_type => $rate) {
            echo '<tr><td><input type="radio" name="shipping" id="gc_shipping" value="' . $shipping_type . '" onclick="set_shipping_cost(this.value);"/>' . '</td>';
            echo '<td>' . $shipping_type . '</td>';
            echo '<td>' . '$' . number_format($rate, 2, '.', ',') . '</td><tr>';
        }
        ?>
                </table>
            </td>
            <td id="gc_shipping_cost" style="background-color:#eeeeee;"></td></tr>



        <tr>
            <td colspan="2" style="background-color:#eeeeee; font-weight:bold;">Order Subtotal</td>
            <td colspan="3" style="background-color:#eeeeee;"></td>
            <td  id="gc_subtotal_price" style="background-color:#eeeeee;"><?php
        echo '$' . number_format($this->go_cart->subtotal(), 2, '.', ',');
        ?></td>
        </tr>
<?php if ($this->go_cart->coupon_discount() > 0) : ?>
            <tr>
                <td colspan="2" style="background-color:#eeeeee; font-weight:bold;">Coupon Discount</td>
                <td colspan="3" style="background-color:#eeeeee;"></td>
                <td  id="gc_coupon_discount" style="background-color:#eeeeee;"><?php
    echo '-$' . number_format($this->go_cart->coupon_discount(), 2, '.', ',');
    ?></td>
            </tr>
                    <?php if ($this->go_cart->order_tax() != 0) : ?> 
                <tr>
                    <td colspan="2" style="background-color:#eeeeee; font-weight:bold;">Discounted Subtotal</td>
                    <td colspan="3" style="background-color:#eeeeee;"></td>
                    <td  id="gc_coupon_discount" style="background-color:#eeeeee;" class="gc_total"><?php
                echo '$' . number_format($this->go_cart->discounted_subtotal(), 2, '.', ',');
                ?></td>
                </tr>
                    <?php endif;

                endif; ?>
                <?php if ($this->go_cart->order_tax() > 0) : ?>
            <tr>
                <td colspan="2" style="background-color:#eeeeee; font-weight:bold;">Tax</td>
                <td colspan="3" style="background-color:#eeeeee;"></td>
                <td  id="gc_tax_price" style="background-color:#eeeeee;"><?php
                if (!$this->config->item('tax_shipping')) {
                    echo '$' . number_format($this->go_cart->order_tax(), 2, '.', ',');
                }
                    ?></td>
            </tr>
                <?php endif; ?>
                <?php if ($this->go_cart->gift_card_discount() > 0) : ?>
            <tr>
                <td colspan="2" style="background-color:#eeeeee; font-weight:bold;">Gift Card</td>
                <td colspan="3" style="background-color:#eeeeee;"></td>
                <td  id="gc_gift_discount" style="background-color:#eeeeee;">
                    -$<?php echo number_format($this->go_cart->gift_card_discount(), 2, '.', ','); ?>                </td>
            </tr>
<?php endif; ?>

        <tr><td colspan="2" style="background-color:#eeeeee; font-weight:bold;">Total</td>
            <td colspan="3" style="background-color:#eeeeee;"></td>
            <td  id="gc_total_price" style="background-color:#eeeeee;" class="gc_total"><?php echo '$' . number_format($this->go_cart->total(), 2, '.', ','); ?></td>
        </tr>



    </tbody>
</table>
<input type="submit" name="submit" value="Confirm Order and Proceed to Payment" />
<?php echo form_close(); ?>
<script type="text/javascript">
	
    function set_shipping_cost(shipping_type)
    {
        var order_total	= '<?php echo number_format($this->go_cart->total(), 2); ?>';
        var shipping	= new Array();
<?php
foreach ($shipping_methods as $shipping_type => $rate) {
    echo 'shipping["' . $shipping_type . '"] = new Array();' . "\r\n\t";
    echo 'shipping["' . $shipping_type . '"]["num"] = ' . number_format($rate, 2) . ';' . "\r\n\t";
    echo 'shipping["' . $shipping_type . '"]["str"] = "$' . number_format($rate, 2, '.', ',') . '";' . "\r\n\t";
}
?>
		
<?php
//if tax is added to shipping, then we adjust the tax cost based on the shipping selection
if ($this->config->item('tax_shipping')):
    ?>
    		
                            var tax_this	= parseFloat((parseFloat(order_total) + parseFloat(shipping[shipping_type]['num']))*<?php echo $tax_rate; ?>);
                            var total_price	= parseFloat(order_total) + parseFloat(shipping[shipping_type]['num'])+tax_this;
    		
                            $('#gc_tax_price').html('$'+tax_this.toFixed(2));
    		
<?php else: ?>
                            var total_price	= parseFloat(order_total) + parseFloat(shipping[shipping_type]['num']);
<?php endif; ?>
		
<? if ($this->go_cart->gift_cards_enabled()) { ?>
                            var gift_balance = <?php echo $this->go_cart->gift_card_balance(); ?>;
                            var gift_discount = <?php echo $this->go_cart->gift_card_discount(); ?>;
<?php } ?>
		
<? if ($this->go_cart->gift_cards_enabled()) { ?>
                            // take from gift card if funds remain
                            if(gift_balance < total_price && gift_balance != 0) { 
                                gift_discount = gift_discount + gift_balance;
                                total_price = total_price - gift_balance;
                                gift_balance = 0;
    			
                                $('#gc_gift_discount').html('-$'+gift_discount.toFixed(2));
                            }
                            else if(gift_balance > total_price) {
                                gift_discount = gift_discount + parseFloat(shipping[shipping_type]['num']);
                                gift_balance = gift_balance - parseFloat(shipping[shipping_type]['num']);
                                total_price = 0;
    			
                                $('#gc_gift_discount').html('-$'+gift_discount.toFixed(2));
                            }
    		
    		
<?php } ?>
		
                        $('#gc_shipping_cost').html(shipping[shipping_type]['str']);
                        $('#gc_total_price').html('$'+total_price.toFixed(2));
                    }
</script>
<?php include('footer.php'); ?>
