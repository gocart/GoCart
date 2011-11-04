<div class="gc_menu">
    <div class="gc_menu_title">Categories</div>
    <?php

    function display_categories($cats) {
        echo '<ul>';
        foreach ($cats as $cat) {

            echo '<li><a href="' . base_url() . 'cart/category/' . $cat['category']->slug . '">' . $cat['category']->name . '</a>';
            if (sizeof($cat['children']) > 0) {
                display_categories($cat['children']);
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    display_categories($this->categories);
    ?>
</div>

<div class="gc_menu">
    <div class="gc_menu_title">Company</div>
    <?php
    //eventually we will have a CMS system built in and it will
    //work here in a similar fashion to the get categories function
    //display_pages($this->pages); 
    ?>
    <ul>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Privacy Statement</a></li>
        <li><a href="#">Contact Us</a></li>
    </ul>
</div>
