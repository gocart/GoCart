<?php include('header.php'); ?>
<p>
    <strong>Customer Information</strong><br/>
    <?php
    if ($customer['company'] != '') {
        echo 'Company: ' . $customer['company'] . '<br/>';
    }
    ?>
    Name: <?php echo $customer['firstname']; ?> <?php echo $customer['lastname']; ?> <br/>
    Email: <?php echo $customer['email']; ?> <br/>
    Phone: <?php echo $customer['phone']; ?>
</p>
<table style="width:100%;">
    <tr>
        <td>
            <strong>Billing Address</strong><br/>
            <?php
            $bill = $customer['bill_address'];

            if (!empty($bill['company']))
                echo $bill['company'] . '<br>';
            echo $bill['firstname'] . ' ' . $bill['lastname'] . ' &lt;' . $bill['email'] . '&gt;<br>';
            echo $bill['phone'] . '<br>';
            echo $bill['address1'] . '<br>';
            if (!empty($bill['address2']))
                echo $bill['address2'] . '<br>';
            echo $bill['city'] . ', ' . $bill['state'] . ' ' . $bill['zip'];
            ?> <br/>
        </td>
        <td>
            <strong>Shipping Address</strong><br/>
            <?php
            $ship = $customer['ship_address'];

            if (!empty($ship['company']))
                echo $ship['company'] . '<br>';
            echo $ship['firstname'] . ' ' . $ship['lastname'] . ' &lt;' . $ship['email'] . '&gt;<br>';
            echo $ship['phone'] . '<br>';
            echo $ship['address1'] . '<br>';
            if (!empty($ship['address2']))
                echo $ship['address2'] . '<br>';
            echo $ship['city'] . ', ' . $ship['state'] . ' ' . $ship['zip'];
            ?> <br/>
        </td>
    </tr>
    <tr><td colspan="2"></td></tr>
    <tr>
        <td>
            <strong>Payment Information</strong><br/>
<?php echo $payment['description']; ?>
        </td>
        <td>
            <strong>Shipping Information</strong><br/>
<?php echo $shipping['method']; ?>
            $<?php echo number_format($shipping['price'], 2, '.', ','); ?>
        </td>
    </tr>
</table>
<table class="gc_view_cart" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>Item #</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
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
                        echo "<div>$name:<BR>";
                        foreach ($value as $item)
                            echo "- $item<BR>";
                        echo "</div>";
                    } else {
                        echo '<div>' . $name . ': ' . $value . '</div>';
                    }
                }
            }
            echo '</td>';

            $price = $product['price'];

            echo '<td>$' . number_format($price, 2, '.', ',') . '</td>' .
            '<td style="text-align:center;">' . $product['quantity'] . '</td>' .
            '<td>$' . number_format($price * $product['quantity'], 2, '.', ',') . '</td></tr>';

            if ($td == 'gc_even') {
                $td = '';
            } else {
                $td = 'gc_even';
            }
        }
        ?>

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
        <tr>
            <td style="background-color:#eeeeee;"><strong>Shipping</strong></td>
            <td style="background-color:#eeeeee;" colspan="4"><?php echo $shipping['method'] ?></td>
            <td style="background-color:#eeeeee;"><?php echo '$' . number_format($shipping['price'], 2, '.', ',') ?></td>
        <tr>

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

        <tr>
            <td colspan="2" style="background-color:#eeeeee; font-weight:bold;">Total</td>
            <td colspan="3" style="background-color:#eeeeee;"></td>
            <td  id="gc_total_price" style="background-color:#eeeeee;" class="gc_total"><?php echo '$' . number_format($this->go_cart->total(), 2, '.', ','); ?></td>
        </tr>


    </tbody>
</table>

<?php
//this is our confirm order button, once clicked the order should be processed
echo secure_form_open('checkout/place_order');
?>
<input type="submit" name="submit" value="Place Order"/>
<?php echo form_close(); ?>
<?php include('footer.php'); ?>
