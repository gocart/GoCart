<?php

if ( ! function_exists('form_open'))
{
	function secure_form_open($action = '', $attributes = '', $hidden = array())
	{
		$CI =& get_instance();

		if ($attributes == '')
		{
			$attributes = 'method="post"';
		}

		$action = ( strpos($action, '://') === FALSE) ? $CI->config->secure_site_url($action) : $action;

		$form = '<form action="'.$action.'"';

		$form .= _attributes_to_string($attributes, TRUE);

		$form .= '>';

		// CSRF
		if ($CI->config->item('csrf_protection') === TRUE)
		{
			$hidden[$CI->security->csrf_token_name] = $CI->security->csrf_hash;
		}

		if (is_array($hidden) AND count($hidden) > 0)
		{
			$form .= sprintf("\n<div class=\"hidden\">%s</div>", form_hidden($hidden));
		}

		return $form;
	}
}

if ( ! function_exists('form_open_multipart'))
{
	function secure_form_open_multipart($action, $attributes = array(), $hidden = array())
	{
		if (is_string($attributes))
		{
			$attributes .= ' enctype="multipart/form-data"';
		}
		else
		{
			$attributes['enctype'] = 'multipart/form-data';
		}

		return secure_form_open($action, $attributes, $hidden);
	}
}