<?php include('header.php'); ?>
<style type="text/css">

    #photos {
        width: 55%;
        float: left;
    }

    #options {
        width:45%;
        float:left;
    }

</style>

<?php echo form_open('cart/add_to_cart'); ?>
<div id="photos">
    <div id="gc_product_image">
        <?php
        //get the primary photo for the product
        $photo = '<img src="' . $this->config->item('template_path') . '/images/no_image.jpg" alt="no image available"/>';
        if (count($product->photos) > 0) {
            $primary = $product->photos[0];
            foreach ($product->photos as $photo) {
                if ($photo->primary) {
                    $primary = $photo;
                }
            }

            $photo = '<img src="/uploads/photos/small/' . $primary->filename . '" alt="' . $product->slug . '"/>';
        }
        echo $photo;
        ?>
    </div>
</div>
<div id="options">
    <table cellpadding="0" cellspacing="0" id="gc_product_form">
        <tr>
            <td align="right">
                <input type="hidden" name="cartkey" value="<?php echo $this->session->flashdata('cartkey'); ?>" />
                <input type="hidden" name="id" value="<?php echo $product->id ?>"/>
<?php if ($this->config->item('allow_os_purchase') == true) : ?>
                    <div>Quantity: <input type="text" name="quantity" size="3" value="1"/>
                        <input type="submit" value="Add To Cart"/></div>
                <?php endif; ?>
                </div>

                <?php if (count($options) > 0): ?>

                    <div id="gc_product_options">
                    <?php
                    echo '<table cellpadding="0" cellspacing="0">';
                    foreach ($options as $option) {
                        $required = '';
                        if ($option->required) {
                            $required = ' gc_required"';
                        }
                        echo '<tr>';
                        echo '<td class="gc_option_name' . $required . '">' . $option->name . ': </td><td class="gc_option">';

                        //
                        //this is where we generate the options and either use default values, or previously posted variables
                        //that we either returned for errors, or in some other releases of Go Cart the user may be editing
                        //and entry in their cart.
                        //if we're dealing with a textfield or text area, grab the option value and store it in value
                        if ($option->type == 'checklist') {
                            $value = array();
                            if ($posted_options && isset($posted_options[$option->id])) {
                                $value = $posted_options[$option->id];
                            }
                        } else {
                            $value = $option->values[0]->value;
                            if ($posted_options && isset($posted_options[$option->id])) {
                                $value = $posted_options[$option->id];
                            }
                        }

                        if ($option->type == 'textfield') {
                            echo '<input type="textfield" id="input_' . $option->id . '" name="option[' . $option->id . ']" value="' . $value . '" />';
                        } elseif ($option->type == 'textarea') {
                            echo '<textarea id="input_' . $option->id . '" name="option[' . $option->id . ']">' . $value . '</textarea>';
                        } elseif ($option->type == 'droplist') {
                            echo '<select name="option[' . $option->id . ']">';
                            echo '<option value="">Choose an Option</option>';

                            foreach ($option->values as $values) {
                                $selected = '';
                                if ($value == $values->id) {
                                    $selected = ' selected="selected"';
                                }
                                echo '<option' . $selected . ' value=\'' . $values->id . '\'>' . $values->name . '</span>  ' . format_currency($values->price) . '  ' . $values->weight . 'lbs</option>';
                            }
                            echo '</select>';
                        } elseif ($option->type == 'radiolist') {
                            foreach ($option->values as $values) {
                                $checked = '';
                                if ($value == $values->id) {
                                    $checked = ' checked="checked"';
                                }
                                echo '<div class="gc_option_list">';
                                echo '<input' . $checked . ' type="radio" name="option[' . $option->id . ']" value="' . $values->id . '"/> ';
                                echo $values->name . '  ' . format_currency($values->price) . '  ' . $values->weight . 'lbs';
                                echo '</div>';
                            }
                        } elseif ($option->type == 'checklist') {
                            foreach ($option->values as $values) {
                                $checked = '';
                                if (in_array($values->id, $value)) {
                                    $checked = ' checked="checked"';
                                }
                                echo '<div class="gc_option_list">';
                                echo '<input' . $checked . ' type="checkbox" name="option[' . $option->id . '][]" value="' . $values->id . '"/> ';
                                echo $values->name . '  ' . format_currency($values->price) . '  ' . $values->weight . 'lbs';
                                echo '</div>';
                            }
                        }

                        echo '</td></tr>';
                    }
                    echo '<tr><td colspan="2" id="gc_options_note">Items marked with * are required.</td></tr>';
                    echo '</table>';
                    ?>
                    </div>
                </td>
                    <?php endif; ?>
        </tr>
    </table>
</div>
</form>
                    <?php include('footer.php'); ?>