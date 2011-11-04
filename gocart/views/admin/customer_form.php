<?php include('header.php'); ?>

<?php echo secure_form_open($this->config->item('admin_folder') . '/customers/form/' . $id); ?>
<div class="button_set">
    <input type="submit" value="Save Customer" />
</div>

<div id="gc_tabs">
    <ul>
        <li><a href="#gc_customer_attributes">Customer Information</a></li>
    </ul>
    <div id="gc_customer_attributes">
        <div>
            <table>
                <tr>
                    <td style="padding-right:30px;" rowspan="2">

                        <table>
                            <tr><td colspan="2"><h3>Personal Information</h3></td></tr>
                            <tr>
                                <td>Company</td>
                                <td>
                                    <?php
                                    $data = array('id' => 'company', 'name' => 'company', 'value' => set_value('company', $company), 'class' => 'gc_tf1');
                                    echo form_input($data);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>First Name</td>
                                <td><?php
                                    $data = array('id' => 'firstname', 'name' => 'firstname', 'value' => set_value('firstname', $firstname), 'class' => 'gc_tf1');
                                    echo form_input($data);
                                    ?></td>
                            </tr>
                            <tr>
                                <td>Last Name</td>
                                <td>
                                    <?php
                                    $data = array('id' => 'lastname', 'name' => 'lastname', 'value' => set_value('lastname', $lastname), 'class' => 'gc_tf1');
                                    echo form_input($data);
                                    ?>					</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>
                                    <?php
                                    $data = array('id' => 'email', 'name' => 'email', 'value' => set_value('email', $email), 'class' => 'gc_tf1');
                                    echo form_input($data);
                                    ?>					</td>
                            </tr>
                            <tr>
                                <td>Phone Number</td>
                                <td>
                                    <?php
                                    $data = array('id' => 'phone', 'name' => 'phone', 'value' => set_value('phone', $phone), 'class' => 'gc_tf1');
                                    echo form_input($data);
                                    ?>					</td>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td>
                                    <?php
                                    $data = array('id' => 'password', 'name' => 'password', 'class' => 'gc_tf1');
                                    echo form_password($data);
                                    ?>					</td>
                            </tr>
                            <tr>
                                <td>Confirm</td>
                                <td>
                                    <?php
                                    $data = array('id' => 'confirm', 'name' => 'confirm', 'class' => 'gc_tf1');
                                    echo form_password($data);
                                    ?>					</td>
                            </tr>
                            <tr>
                                <td>Email Subscribed</td>
                                <td><input type="checkbox" name="email_subscribe" value="1" <?php if ((bool) $email_subscribe) { ?> checked="checked" <?php } ?>/></td>
                            </tr>
                            <tr>
                                <td>Active</td>
                                <td>
                                    <?php
                                    $data = array('id' => 'active', 'name' => 'active', 'value' => 1, 'checked' => $active);
                                    echo form_checkbox($data);
                                    ?>					</td>
                            </tr>
                            <tr>
                                <td>Group</td>
                                <td><? echo form_dropdown('group_id', $group_list, set_value('group_id', $group_id)); ?></td>
                            </tr>
                        </table>
                    </td>

                </tr>
            </table>
        </div>

    </div>

</form>

<?php
include('footer.php');