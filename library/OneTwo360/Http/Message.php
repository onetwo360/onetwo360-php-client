<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http;

/**
 * Message
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
abstract class Message implements MessageInterface
{
	const VERSION_10 = '1.0';
	const VERSION_11 = '1.1';
	
	/**
	 * Array of headers
	 *
	 * @var array
	 */
	protected $headers = array();
	
	/**
	 *
	 * @var string
	 */
	protected $content = '';
	
	/**
	 * @var string
	 */
	protected $version = self::VERSION_11;
	
	/**
	 * 
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}
	
	/**
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getHeader($name, $default = false)
	{
		$headers = $this->getHeaders();
		if (array_key_exists($name, $headers))
			return $headers[$name];
		return $default;
	}
	
	/**
	 * 
	 * @param array|string $headers
	 * @return Message
	 * @throws Exception\InvalidArgumentException
	 */
	public function setHeaders($headers)
	{
		if (is_string($headers))
			$headers = $this->headersStringToArray($headers);
		elseif (!is_array($headers))
			throw new Exception\InvalidArgumentException('$headers must either be a string or an array');
		$this->headers = $headers;
		return $this;
	}
	
	/**
	 * Insert, append or replace a specific header
	 * 
	 * @param string $name
	 * @param string|array $value
	 * @param bool $replace
	 * @return Message
	 */
	public function addHeader($name, $value, $replace = true)
	{
		if (array_key_exists($name, $this->headers) && !is_array($this->headers[$name]) && !$replace) {
			$firstVal = $this->headers[$name];
			$this->headers[$name] = array($firstVal, $value);
		} elseif (array_key_exists($name, $this->headers) && !$replace)
			$this->headers[$name][] = $value;
		else
			$this->headers[$name] = $value;
		return $this;
	}
	
	/**
	 * 
	 * @param string $headers
	 * @return array
	 */
	protected function headersStringToArray($headers)
	{
		$headers = explode("\r\n", $headers);
		$headersArray = array();
		foreach ($headers as $header) {
			$tmp = explode(':', $header, 2);
			$key = trim($tmp[0]);
			$value = isset($tmp[1]) ? trim($tmp[1]) : true;
			if (array_key_exists($key, $headersArray) && !is_array($headersArray[$key])) {
				$firstVal = $headersArray[$key];
				$headersArray[$key] = array($firstVal, $value);
			} elseif (array_key_exists($key, $headersArray))
				$headersArray[$key][] = $value;
			else
				$headersArray[$key] = $value;
		}
		return $headersArray;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function renderHeaders()
	{
		$headers = $this->getHeaders();
		if (!count($headers))
			return "\r\n";
		
		$str = '';
		foreach ($headers as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $innerV)
					$str .= $k . ': ' . $innerV . "\r\n";
			} else
				$str .= $k . ': ' . $v . "\r\n";
		}
		return $str;
	}
	
	public function getContent()
	{
		return $this->content;
	}
	
	public function setContent($content)
	{
		$this->content = (string) $content;
		return $this;
	}
	
	public function getVersion()
	{
		return $this->version;
	}
	
	public function setVersion($version)
	{
		if ($version != self::VERSION_10 && $version != self::VERSION_11) {
			throw new Exception\InvalidArgumentException(
				'Not valid or not supported HTTP version: ' . $version
			);
		}
		$this->version = $version;
		return $this;
	}
}