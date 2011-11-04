<?php include('header.php'); ?>

<h3>Most Recent Orders</h3>
<table class="gc_table" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th class="gc_cell_left">Order</th>
            <th>Bill To</th>
            <th>Ship To</th>
            <th>Ordered On</th>
            <th>Status</th>
            <th class="gc_cell_right">Notes</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td  class="gc_cell_left"><a href="<?php echo base_url(); ?><?php echo $this->config->item('admin_folder'); ?>/orders/view/<?php echo $order->id; ?>"><?php echo $order->order_number; ?></a></td>
                <td><?php echo $order->bill_lastname . ', ' . $order->bill_firstname; ?></td>
                <td><?php echo $order->ship_lastname . ', ' . $order->ship_firstname; ?></td>
                <td><?php echo format_date($order->ordered_on); ?></td>
                <td style="width:150px;">
                    <?php echo $order->status ?> 

                </td>
                <td class="gc_cell_right"><div class="MainTableNotes"><?php echo $order->notes; ?></div></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br /><br />


<h3>Most Recent Registered Customers</h3>
<table class="gc_table" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <?php /* <th>ID</th> uncomment this if you want it */ ?>
            <th class="gc_cell_left">First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th class="gc_cell_right">Active</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer): ?>
            <tr>
                <?php /* <td style="width:16px;"><?php echo  $customer->id; ?></td> */ ?>
                <td class="gc_cell_left"><?php echo $customer->firstname; ?></td>
                <td><?php echo $customer->lastname; ?></td>
                <td><a href="mailto:<?php echo $customer->email; ?>"><?php echo $customer->email; ?></a></td>
                <td>
                    <?php
                    if ($customer->active == 1) {
                        echo 'Yes';
                    } else {
                        echo 'No';
                    }
                    ?>
                </td>

            </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php include('footer.php'); ?>
