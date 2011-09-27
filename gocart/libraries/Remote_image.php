<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class remote_image {

	var $source;
	var $save_to;
	var $set_extension;
	var $quality;
	var $CI;
	function __construct()
	{
		$this->CI =& get_instance();
	}
	
	function download($source, $save_to, $quality, $method = 'curl') // default method: cURL
	{
		
		$this->source			= $source;
		$this->save_to			= $save_to;
		$this->quality			= $quality;
		
		
		$info = @GetImageSize($this->source);
		$mime = $info['mime'];

		// What sort of image?
		$type = substr(strrchr($mime, '/'), 1);

		switch ($type)
		{
			case 'jpeg':
				$image_create_func = 'ImageCreateFromJPEG';
				break;
			
			case 'png':
				$image_create_func = 'ImageCreateFromPNG';
				break;
			
			case 'bmp':
				$image_create_func = 'ImageCreateFromBMP';
				break;
			
			case 'gif':
				$image_create_func = 'ImageCreateFromGIF';
				break;
			
			default:
				$image_create_func = 'ImageCreateFromJPEG';
		}
		
		//make all the images jpg's
		$image_save_func = 'ImageJPEG';
		$new_image_ext = 'jpg';

		// Best Quality: 100
		$quality = isSet($this->quality) ? $this->quality : 100;
		
		$ext = strrchr($this->source, ".");
		$strlen = strlen($ext);
		$new_name = md5(time().basename(substr($this->source, 0, -$strlen))).'.'.$new_image_ext;

		$save_to = $this->save_to.$new_name;

	    if($method == 'curl')
		{
	    $save_image = $this->LoadImageCURL($save_to);
		}
		elseif($method == 'gd')
		{
		$img = $image_create_func($this->source);

		    if(isSet($quality))
		    {
			   $save_image = $image_save_func($img, $save_to, $quality);
			}
			else
			{
			   $save_image = $image_save_func($img, $save_to);
			}
		}
		
		
		//ok this may be a little whack, but we're going to resize now to make a smaller file
		$config['source_image']		= $save_to.$new_name;
		$config['library_path']		= '/usr/bin/convert';
		$config['width']			= 800;
		$config['height']			= 800;
		$config['quality']			= '85%';
		$config['image_library']	= 'ImageMagick';
		$config['maintain_ratio']	= TRUE;
		$this->CI->load->library('image_lib', $config);
		$this->CI->image_lib->resize();
		
		
		return $new_name;
	}
	
	function LoadImageCURL($save_to)
	{
		$ch = curl_init($this->source);
		$fp = fopen($save_to, "wb");

		// set URL and other appropriate options
		$options = array(CURLOPT_FILE => $fp,
		                 CURLOPT_HEADER => 0,
		                 CURLOPT_FOLLOWLOCATION => 1,
			             CURLOPT_TIMEOUT => 60); // 1 minute timeout (should be enough)

		curl_setopt_array($ch, $options);

		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}
}