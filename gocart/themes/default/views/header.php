<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo (isset($seo_title))?$seo_title:$this->config->item('company_name'); ?></title>


<?php if(isset($meta)):?>
	<?php echo $meta;?>
<?php else:?>
<meta name="Keywords" content="Shopping Cart, eCommerce, Code Igniter">
<meta name="Description" content="Go Cart is an open source shopping cart built on the Code Igniter framework">
<?php endif;?>

<link href="<?php echo base_url('gocart/themes/'.$this->config->item('theme').'/css/styles.css');?>" type="text/css" rel="stylesheet"/> 

<link type="text/css" href="<?php echo base_url('js/jquery/theme/smoothness/jquery-ui-1.8.16.custom.css');?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo base_url('js/jquery/jquery-1.7.1.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jquery/jquery-ui-1.8.16.custom.min.js');?>"></script>

<link type="text/css" href="<?php echo base_url('js/jquery/colorbox/colorbox.css');?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo base_url('js/jquery/colorbox/jquery.colorbox-min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jquery/equal_heights.js');?>"></script>

<script type="text/javascript"> 
 
    $(document).ready(function(){ 	
		$('input:submit, input:button, button, .btn').button();
		$('input:text, input:password').addClass('input');
    }); 
 
</script>


<script type="text/javascript"> 
var $buoop = {} 
$buoop.ol = window.onload; 
window.onload=function(){ 
 try {if ($buoop.ol) $buoop.ol();}catch (e) {} 
 var e = document.createElement("script"); 
 e.setAttribute("type", "text/javascript"); 
 e.setAttribute("src", "https://browser-update.org/update.js"); 
 document.body.appendChild(e); 
} 
</script> 


<?php
//with this I can put header data in the header instead of in the body.
if(isset($additional_header_info))
{
	echo $additional_header_info;
}

?>
</head>
<body>
<div id="top_menu_container" class="full_wrap">
	<div class="wide_wrap">
		<ul id="top_menu" class="right">	
		<?php
		function page_loop($pages, $layer, $first=false)
		{
			if($first)
			{
				echo '<ul'.$first.'>';
			}
			

			foreach($pages as $page):?>

				<li>
				<?php if(empty($page->content)):?>
					<a href="<?php echo $page->url;?>" <?php if($page->new_window ==1){echo 'target="_blank"';} ?>><?php echo $page->menu_title;?></a>
				<?php else:?>
					<a href="<?php echo site_url($page->slug);?>"><?php echo $page->menu_title;?></a>
				<?php endif;
				if($layer == 1)
				{
					$next = $layer+1;
					if(!empty($page->children))
					{
						page_loop($page->children, $next, ' class="first"');
					}
				}
				else
				{
					$next = $layer+1;
					if(!empty($page->children))
					{
						page_loop($page->children, $next, ' class="nav"');
					}
				}?>
				</li>
			<?php	
			endforeach;
			if($first)
			{
				echo '</ul>';
			}
			
		}
		page_loop($this->pages, 1);
		
		?>
		
		<?php if($this->Customer_model->is_logged_in(false, false)):?>
			<li class="bold begin_user_menu"><a href="<?php echo site_url('secure/logout');?>"><?php echo lang('logout');?></a></li>
			<li class="bold"><a href="<?php echo  site_url('secure/my_account');?>"><?php echo lang('my_account')?></a></li>
			<li class="bold"><a href="<?php echo  site_url('secure/my_downloads');?>"><?php echo lang('my_downloads')?></a></li>
		<?php else: ?>
			<li class="bold begin_user_menu"><a href="<?php echo site_url('secure/login');?>"><?php echo lang('login');?></a></li>
		<?php endif; ?>
		<li class="bold">
			<a href="<?php echo site_url('cart/view_cart');?>">
			<?php
			if ($this->go_cart->total_items()==0)
			{
				echo lang('empty_cart');
			}
			else
			{
				if($this->go_cart->total_items() > 1)
				{
					echo sprintf (lang('multiple_items'), $this->go_cart->total_items());
				}
				else
				{
					echo sprintf (lang('single_item'), $this->go_cart->total_items());
				}
			}
			?>
			</a>
		</li>
			
		</ul>
	</div>
	
	<div class="clear"></div>
</div>

<div class="full_wrap">
	<div id="header" class="wide_wrap">

		<div id="search_form" class="right">
			<?php echo form_open('cart/search');?>
				<input type="text" name="term"/>
				<button type="submit"><?php echo lang('form_search');?></button>
			</form>
		</div>
		
		<a href="<?php echo base_url();?>">
			<img src="<?php echo base_url('images/logo.png');?>" alt="<?php echo $this->config->item('company_name'); ?>">
		</a>
		
	</div>
</div>

<div class="wide_wrap">
	
	<img src="<?php echo base_url('images/menu_left_wrap.gif');?>" alt="left" class="main_menu_left_wrap"/>
	<img src="<?php echo base_url('images/menu_right_wrap.gif');?>" alt="right" class="main_menu_right_wrap"/>
	
	<div id="main_menu">
		<ul id="nav">
		<?php
		function display_categories($cats, $layer, $first='')
		{
			if($first)
			{
				echo '<ul'.$first.'>';
			}
			
			foreach ($cats as $cat)
			{

				echo '<li><a href="'.site_url($cat['category']->slug).'">'.$cat['category']->name.'</a>'."\n";
				if (sizeof($cat['children']) > 0)
				{
					if($layer == 1)
					{
						$next = $layer+1;
						display_categories($cat['children'], $next, ' class="first"');
					}
					else
					{
						$next = $layer+1;
						display_categories($cat['children'], $next, ' class="nav"');
					}
				}
				echo '</li>';
			}
			if($first)
			{
				echo '</ul>';
			}	
		}
			
		display_categories($this->categories, 1);
		
		
		
		if($gift_cards_enabled):?>
			<li><a href="<?php echo site_url('cart/giftcard');?>"><?php echo lang('giftcard');?></a></li>
		<?php endif;?>
		</ul>
		
		<br class="clear" />
	</div>
</div>

<div class="wide_wrap">
	<div class="content_wrap">
		<?php if (!empty($page_title)):?><h1><?php echo $page_title; ?></h1><?php endif;?>
	
		<?php
		if ($this->session->flashdata('message'))
		{
			echo '<div class="gmessage">'.$this->session->flashdata('message').'</div>';
		}
		if ($this->session->flashdata('error'))
		{
			echo '<div class="error">'.$this->session->flashdata('error').'</div>';
		}
		if (!empty($error))
		{
			echo '<div class="error">'.$error.'</div>';
		}
		

/*
End header.php file
*/