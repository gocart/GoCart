<?php include('header.php'); ?>
<script type="text/javascript">
    function areyousure()
    {
        return confirm('Are you sure you want to delete this category?');
    }
</script>

<div class="button_set">
    <a class="button" href="<?php echo base_url(); ?><?php echo $this->config->item('admin_folder'); ?>/categories/form">Add New Category</a>
</div>

<table class="gc_table" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th class="gc_cell_left">ID</th>
            <th>Name</th>
            <th class="gc_cell_right"></th>
        </tr>
    </thead>
    <tbody>
        <?php echo (count($categories) < 1) ? '<tr><td style="text-align:center;" colspan="3">There are currently no categories.</td></tr>' : '' ?>
        <?php
        define('ADMIN_FOLDER', $this->config->item('admin_folder'));

        function list_categories($cats, $sub='') {

            foreach ($cats as $cat):
                ?>
                <tr class="gc_row">
                    <td class="gc_cell_left" style="width:16px;"><?php echo $cat['category']->id; ?></td>
                    <td><?php echo $sub . $cat['category']->name; ?></td>
                    <td class="gc_cell_right list_buttons">
                        <a href="<?php echo base_url(); ?><?php echo ADMIN_FOLDER; ?>/categories/delete/<?php echo $cat['category']->id; ?>" onclick="return areyousure();">Delete</a>

                        <a href="<?php echo base_url(); ?><?php echo ADMIN_FOLDER; ?>/categories/form/<?php echo $cat['category']->id; ?>" class="ui-state-default ui-corner-all">Edit</a>

                        <a href="<?php echo base_url(); ?><?php echo ADMIN_FOLDER; ?>/categories/organize/<?php echo $cat['category']->id; ?>">Organize</a>
                    </td>
                </tr>
                <?php
                if (sizeof($cat['children']) > 0) {
                    $sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
                    $sub2 .= '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
                    list_categories($cat['children'], $sub2);
                }
            endforeach;
        }

        list_categories($categories);
        ?>
    </tbody>
</table>
<?php include('footer.php'); ?>