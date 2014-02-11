<?php

/*
 * Copyright (c) 2014, Hammerti.me
 */

namespace OneTwo360\Http;

/**
 * Request
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class Request extends Message implements RequestInterface
{
	protected $method = self::METHOD_GET;
	
	protected $uri;

	protected $queryParams = array();
	
	protected $postParams = array();
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function setMethod($method)
	{
		$method = strtoupper($method);
		if (!defined('static::METHOD_' . $method)) {
			throw new Exception\InvalidArgumentException('Invalid HTTP method passed');
		}
		$this->method = $method;
		return $this;
	}
	
	public function getUri()
	{
		return $this->uri;
	}
	
	public function setUri($uri)
	{
		$this->uri = (string) $uri;
		return $this;
	}
	
	public function getQueryParams()
	{
		return $this->queryParams;
	}
	
	public function setQueryParams(array $params)
	{
		$this->queryParams = $params;
		return $this;
	}
	
	public function getQueryParam($name, $default = null)
	{
		$queryParams = $this->getQueryParams();
		if (array_key_exists($name, $queryParams))
			return $queryParams[$name];
		return $default;
	}
	
	public function addQueryParam($name, $value, $replace = true)
	{
		if (array_key_exists($name, $this->queryParams) && !is_array($this->queryParams[$name]) && !$replace) {
			$firstVal = $this->queryParams[$name];
			$this->queryParams[$name] = array($firstVal, $value);
		} elseif (array_key_exists($name, $this->queryParams) && !$replace)
			$this->queryParams[$name][] = $value;
		else
			$this->queryParams[$name] = $value;
		return $this;
	}
	
	public function getPostParams()
	{
		return $this->postParams;
	}
	
	public function setPostParams(array $params)
	{
		$this->postParams = $params;
		return $this;
	}
	
	public function getPostParam($name, $default = null)
	{
		$postParams = $this->getPostParams();
		if (array_key_exists($name, $postParams))
			return $postParams[$name];
		return $default;
	}
	
	public function addPostParam($name, $value, $replace = true)
	{
		if (array_key_exists($name, $this->postParams) && !is_array($this->postParams[$name]) && !$replace) {
			$firstVal = $this->postParams[$name];
			$this->postParams[$name] = array($firstVal, $value);
		} elseif (array_key_exists($name, $this->postParams) && !$replace)
			$this->postParams[$name][] = $value;
		else
			$this->postParams[$name] = $value;
		return $this;
	}

	/**
	 * Is this an OPTIONS method request?
	 *
	 * @return bool
	 */
	public function isOptions()
	{
		return ($this->method === self::METHOD_OPTIONS);
	}

	/**
	 * Is this a PROPFIND method request?
	 *
	 * @return bool
	 */
	public function isPropFind()
	{
		return ($this->method === self::METHOD_PROPFIND);
	}

	/**
	 * Is this a GET method request?
	 *
	 * @return bool
	 */
	public function isGet()
	{
		return ($this->method === self::METHOD_GET);
	}

	/**
	 * Is this a HEAD method request?
	 *
	 * @return bool
	 */
	public function isHead()
	{
		return ($this->method === self::METHOD_HEAD);
	}

	/**
	 * Is this a POST method request?
	 *
	 * @return bool
	 */
	public function isPost()
	{
		return ($this->method === self::METHOD_POST);
	}

	/**
	 * Is this a PUT method request?
	 *
	 * @return bool
	 */
	public function isPut()
	{
		return ($this->method === self::METHOD_PUT);
	}

	/**
	 * Is this a DELETE method request?
	 *
	 * @return bool
	 */
	public function isDelete()
	{
		return ($this->method === self::METHOD_DELETE);
	}

	/**
	 * Is this a TRACE method request?
	 *
	 * @return bool
	 */
	public function isTrace()
	{
		return ($this->method === self::METHOD_TRACE);
	}

	/**
	 * Is this a CONNECT method request?
	 *
	 * @return bool
	 */
	public function isConnect()
	{
		return ($this->method === self::METHOD_CONNECT);
	}

	/**
	 * Is this a PATCH method request?
	 *
	 * @return bool
	 */
	public function isPatch()
	{
		return ($this->method === self::METHOD_PATCH);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function renderRequestLine()
	{
		return $this->method . ' ' . (string) $this->uri . ' HTTP/' . $this->version;
	}

	/**
	 * 
	 * @return string
	 */
	public function toString()
	{
		$str = $this->renderRequestLine() . "\r\n";
		$str .= $this->renderHeaders();
		$str .= "\r\n";
		$str .= $this->getContent();
		return $str;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}
}