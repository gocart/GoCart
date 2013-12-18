<?php
/**
 * HTTP Request class
 *
 * @version 1.0
 * @author martin maly
 * @copyright (C) 2008 martin maly
 */
  /*   Modified for compatibility with GoCart by Clear Sky Designs */
/**
 * Class  HTTPRequest
 *
 * @version 1.0
 * @author martin maly
 * @copyright (C) 2008 martin maly
 * 2.10.2008 20:10:40
 */


class HTTPRequest {

	private $host;
	private $path;
	private $port;
	private $rawhost;
	public $ssl = true;

	private $header;
	private $content;
	private $parsedHeader;
	private $CI;

	function __construct() {
		$this->CI =& get_instance();
		$this->host = $this->CI->paypal->host;
		$this->rawhost = $this->ssl ? "ssl://$this->host" : $this->host;
		$this->path = $this->CI->paypal->endpoint;
		if (!$this->ssl) $this->port = 80; else $this->port = 443;
	}

	public function connect( $data = ''){
		$fp = fsockopen($this->rawhost, $this->port);
		if (!$fp) return false;
		fputs($fp, "POST $this->path HTTP/1.1\r\n");
		fputs($fp, "Host: $this->host\r\n");
		fputs($fp, "Content-length: ".strlen($data)."\r\n");
		fputs($fp, "Connection: close\r\n");
		fputs($fp, "\r\n");
		fputs($fp, $data);

		$responseHeader = '';
		$responseContent = '';

		do
		{
			$responseHeader.= fread($fp, 1);
		}
		while (!preg_match('/\\r\\n\\r\\n$/', $responseHeader));
			
			
		if (!strstr($responseHeader, "Transfer-Encoding: chunked"))
		{
			while (!feof($fp))
			{
				$responseContent.= fgets($fp, 128);
			}
		}
		else
		{

			while ($chunk_length = hexdec(fgets($fp)))
			{
				$responseContentChunk = '';
				 
				$read_length = 0;
				 
				while ($read_length < $chunk_length)
				{
					$responseContentChunk .= fread($fp, $chunk_length - $read_length);
					$read_length = strlen($responseContentChunk);
				}

				$responseContent.= $responseContentChunk;
				 
				fgets($fp);
				 
			}
			 
		}

		$this->header = chop($responseHeader);
		$this->content = $responseContent;
		$this->parsedHeader = $this->headerParse();
		
		$code = intval(trim(substr($this->parsedHeader[0], 9)));

		return $code;
	}

	function headerParse(){
		$h = $this->header;
		$a=explode("\r\n", $h);
		$out = array();
		foreach ($a as $v){
			$k = strpos($v, ':');
			if ($k) {
				$key = trim(substr($v,0,$k));
				$value = trim(substr($v,$k+1));
				if (!$key) continue;
				$out[$key] = $value;
			} else
			{
				if ($v) $out[] = $v;
			}
		}
		return $out;
	}

	public function getContent() {return $this->content;}
	public function getHeader() {return $this->parsedHeader;}
	

}