<?php include('header.php'); ?>

<style type="text/css">
    .sortable_container
    {
        list-style-type:none;
        min-height:10px;
    }

    .sortable_container li
    {
        list-style-type:none;
        clear:both;
    }

    .sortable_container li ul
    {
        margin-left:10px;
    }



    .sortable
    {
        border:1px solid #ccc;
        margin:10px 0px;
        padding:8px;
        font-size:14px;
        font-weight:bold;
        background-image:url(<?php echo secure_base_url(); ?>assets/images/admin/menu_builder_bg.gif);
        background-repeat:repeat-x;
        -moz-border-radius: 7px;
        -webkit-border-radius: 7px;
        -khtml-border-radius: 7px;
        border-radius: 7px;

    }

    .sortable span
    {
        float:left;
        padding-top:2px;
    }

    .handle {
        cursor:pointer;
    }

    .drophover {
        border:1px dashed #ccc;
    }
</style>
<button type="button" id="save" class="gc_button2 ui-state-default ui-corner-all gc_save gc_right">Save Menu</button>

<button type="button" id="add_item" style="margin-right:10px;" class="gc_button2 ui-state-default ui-corner-all gc_save gc_right">Add Item</button>

<div style="font-weight:bold;"><?php echo $menu->name; ?> Menu Editor</div>

<ul class="sortable_container" id="sortable_container">
    <?php
    $count = 0;

    function loop_menu($menu) {
        foreach ($menu as $item):
            ?>

            <li>
                <div class="sortable">
                    <img class="handle" src="<?php echo secure_base_url(); ?>assets/images/admin/grip.gif" style="float:left;margin-right:10px;"/>
                    <span rel="<?php echo $item->url; ?>"><?php echo $item->name; ?></span><br style="clear:both;"/>
                </div>
                <?php
                if (count($item->children) > 0) {
                    loop_menu($item->children);
                }
                $count++;
                ?>
                <ul class="sortable_container" style="margin-left:10px; border:1px solid #000;">
                </ul>
            </li>
            <?php
        endforeach;
    }

    loop_menu($menu->items);
    ?>
</ul>

<script type="text/javascript">
    var count = <?php echo $count; ?>;
    $(document).ready(function(){
        $('#add_item').click(function(){
            $.fn.colorbox({inline:true, href:"#menu_form", scrolling:false});
        });
	
        $('#cancel_item').click(function(){
            clear_form();
        });
	
        $('#save_item').click(function(){
            //validate the form
            if(valid_name() && valid_url($('#form_url').val()))
            {
                add_item($('#form_name').val(), $('#form_url').val());
                clear_form();
            }		
        });

    });

    function clear_form()
    {
        //clear the form values
        $('#form_name').val('');
        $('#form_url').val('');
	
        //close the colorbox
        $.fn.colorbox.close();
    }

    function set_url(slug)
    {
        if(slug != '')
        {
            $('#form_url').val('<?php echo secure_base_url(); ?>'+slug);
        }
        else
        {
            $('#form_url').val('');
        }
    }

    function add_item(item_name, url)
    {
        $('#sortable_container').append('<li><div class="sortable"><img class="handle" src="<?php echo secure_base_url(); ?>assets/images/admin/grip.gif" style="float:left;margin-right:10px;"/><span rel="'+url+'">'+item_name+'</span><br style="clear:both;"/></div><ul class="sortable_container" style="margin-left:10px;"></ul></li>');
        enable_sortables();
    }

    function enable_sortables()
    {
        //destroy the old
        $('.sortable_container').droppable('destroy');
        $(".sortable_container>li").draggable('destroy');
	
        //create the new
        $('.sortable_container').droppable({	hoverClass: 'drophover',
            drop: function(ev, ui) {
                alert($(ui).html())
            }
        });
        $(".sortable_container>li").draggable({
            connectToSortable: '.sortable_container',
            axis: 'y',
            handle:'.handle'
        });
	
	
    }

    function valid_name()
    {
        if($('#form_name').val().trim() != '')
        {
            return true;
        }
        else
        {
            alert("You must enter a Link Name.");
            return false;
        }
    }

    function valid_url(form) {
        var v = new RegExp();
        v.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
        if (v.test(form))
        {
            return true;
        }
        else if(form = '#')
        {
            return true;
        }
        else
        {
            alert("You must supply a valid URL or #.");
            return false;
        }
    }
</script>

<?php //We're just going to do the form inline and hide it no ajax needed. ?>
<span style="display:none">
    <div id="menu_form" style="padding:10px;">
        <p>
            Link Name<br/>
            <input type="text" name="name" id="form_name" />
        </p>
        <p>
            URL<br/>
            <select name="pages" onchange="set_url($(this).val());">
                <option value="">Generate Page URL</option>
                <?php foreach ($pages as $page): ?>
                    <option value="<?php echo $page->slug; ?>"><?php echo $page->title; ?></option>
                <?php endforeach; ?>
            </select><br/>
            <input type="text" name="name" id="form_url" />
        </p>
        <p>
            <button type="button" id="save_item" class="gc_button2 ui-state-default ui-corner-all gc_save">Save</button>
            <button type="button" id="cancel_item" class="gc_button ui-state-default ui-corner-all">Cancel</button>
        </p>
    </div>
</span>
<?php include('footer.php'); ?>
