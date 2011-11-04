<div class="checkout_block">
    <div id="shipping_section">
        <h3>Shipping Method</h3>
        <div class="error" id="shipping_error_box" style="display:none"></div>
        <div id="shipping_method_list">
            <?php if ($this->go_cart->requires_shipping()) { ?>
                <table>
                    <?php foreach ($shipping_methods as $key => $val): ?>
                        <tr>
                            <td><input type="radio" id="<?php echo $key; ?>" class="ship_option" name="shipping_input" value="<?php echo $key; ?>:<?php echo $val['num']; ?>" onclick="set_shipping_cost('<?php echo $key; ?>')"/></td>
                            <td><?php echo $key; ?></td>
                            <td><?php echo $val['str']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php } else { ?>
                Your order does not include any items that require shipping.
            <?php } ?>
        </div>
    </div>
    <div class="clear"></div>
</div>