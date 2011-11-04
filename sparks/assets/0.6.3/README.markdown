# Simple Assets Library

A simple assets library that has the ability to combine and minify your JavaScript and CSS assets.
Additionally there's a <a href="http://leafo.net/lessphp/">LessPHP</a> compiler, based on the original Ruby implementation.

## Third Party Libraries

The libraries <a href="http://code.google.com/p/jsmin-php/">JSMin</a>, <a href="http://code.google.com/p/minify/">CSSMin</a> and <a href="http://leafo.net/lessphp/">LessPHP</a> are all created by third parties, but they're included in this package for convinience.

## Requirements

1. PHP 5.1+
2. CodeIgniter 2.0
3. Directory structure for the assets files, with a writeable cache directory

## Documentation

Set all your preferences in the config file (assets directories, options to minify, combine and parse with LessPHP).
Now you can use the helper methods in your views like this:
	
	<?php display_css(array('init.css', 'style.css')); ?>
	<?php display_js(array('libs/modernizr-1.6.js', 'libs/jquery-1.4.4.js', 'plugins.js', 'script.js')); ?>

There's also a method for clearing all cached files:
	
	$this->assets->clear_cache();

The default configuration assumes your assets directory is in the root of your project. Be sure to set the permissions for the cache directory so it can be writeable.

## Directory structure example

	/application
	/assets
		/cache
		/css
		/images
		/js
	/sparks
	/system
