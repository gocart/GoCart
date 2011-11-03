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

<link href="/assets/css/styles.css" type="text/css" rel="stylesheet"/> 

<?php
//test for http / https for non hosted files
$http = 'http';
if(isset($_SERVER['HTTPS']))
{
	$http .= 's';
}
?>
<link href="<?php echo $http;?>://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/smoothness/jquery-ui.css" type="text/css" rel="stylesheet"/> 
<script type="text/javascript" src="<?php echo $http;?>://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script> 
<script type="text/javascript" src="<?php echo $http;?>://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>


<link type="text/css" href="/assets/js/jquery/colorbox/colorbox.css" rel="stylesheet" />
<script type="text/javascript" src="/assets/js/jquery/colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="/assets/js/jquery/equal_heights.js"></script>

<script type="text/javascript"> 
 
    $(document).ready(function(){ 	
		$('input:submit, input:button, button, .btn').button();
		$('input:text, input:password').addClass('input');
    }); 
 
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
	
<?php if(is_ie(8)):?>
<div class="full_wrap">
	You are using an old browser that may not function as expected.	For a better, safer browsing experience, please upgrade your browser.<br/>
	For your convenience, here are some links!
	<strong><a style="color:#fff;" href="http://windows.microsoft.com/en-US/internet-explorer/downloads/ie">Internet Explorer</a> | 
	<a style="color:#fff;" href="http://firefox.com">Firefox</a> | 
	<a style="color:#fff;" href="http://google.com/chrome">Google Chrome</a> | 
	<a style="color:#fff;" href="http://www.apple.com/safari/">Apple Safari</a></strong>
</div>
<?php endif;?>

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
			

			foreach($pages as $page): if($page->id != 143)://this is the "homepage" page. By skipping it, we're skipping anything under it.?>

				<li>
				<?php if(empty($page->content)):?>
					<a href="<?php echo $page->url;?>" <?php if($page->new_window ==1){echo 'target="_blank"';} ?>><?php echo $page->menu_title;?></a>
				<?php else:?>
					<a href="<?php echo base_url();?><?php echo $page->slug;?>"><?php echo $page->menu_title;?></a>
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
			endif; endforeach;
			if($first)
			{
				echo '</ul>';
			}
			
		}
		page_loop($this->pages, 1);
		
		?>
		
		<?php if($this->Customer_model->is_logged_in(false, false)):?>
			<li class="bold begin_user_menu"><a href="<?php echo base_url();?>secure/logout">Logout</a></li>
			<li class="bold"><a href="<?php echo  secure_base_url();?>secure/my_account">My Account</a></li>
		<?php else: ?>
			<li class="bold begin_user_menu"><a href="<?php echo secure_base_url();?>secure/login">Login</a></li>
		<?php endif; ?>
		<li class="bold">
			<a href="/cart/view_cart">
			<?php
			if ($this->go_cart->total_items()==0)
			{
				echo 'Your cart is empty';
			}
			else
			{
				if($this->go_cart->total_items() > 1)
				{
					echo 'There are '. $this->go_cart->total_items(). ' items in your cart';
				}
				else
				{
					echo 'There is '. $this->go_cart->total_items() .' item in your cart';
				}
			}
			?>
			</a>
		<li>
			
		</ul>
	</div>
	
	<div class="clear"></div>
</div>

<div class="full_wrap">
	<div id="header" class="wide_wrap">

		<div id="search_form" class="right">
			<?php echo form_open('cart/search');?>
				<input type="text" name="term"/>
				<button type="submit">Search</button>
			</form>
		</div>
		
		<a href="<?php echo base_url();?>">
			<img src="/images/logo.png" alt="<?php echo $this->config->item('company_name'); ?>">
		</a>
		
	</div>
</div>

<div class="wide_wrap">
	
	<img src="/images/menu_left_wrap.gif" alt="left" class="main_menu_left_wrap"/>
	<img src="/images/menu_right_wrap.gif" alt="right" class="main_menu_right_wrap"/>
	
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

				echo '<li><a href="'.base_url().''.$cat['category']->slug.'">'.$cat['category']->name.'</a>'."\n";
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
		
		
		
		if($gift_cards_enabled)
		{
			echo '<li><a href="'.base_url().'cart/giftcard">Gift Card</a></li>';
		}
		?>
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