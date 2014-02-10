<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Client\Exception;

use OneTwo360\Http\RequestInterface;
use OneTwo360\Http\ResponseInterface;

/**
 * JsonDecodeException
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class JsonDecodeException extends RuntimeException
{
	/**
	 *
	 * @var RequestInterface
	 */
	protected $request;
	
	/**
	 *
	 * @var ResponseInterface
	 */
	protected $response;
	
	/**
	 * 
	 * @return RequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}
	
	/**
	 * 
	 * @param RequestInterface $request
	 * @return JsonDecodeException
	 */
	protected function setRequest(RequestInterface $request)
	{
		$this->request = $request;
		return $this;
	}
	
	/**
	 * 
	 * @return ResponseInterface
	 */
	public function getResponse()
	{
		return $this->response;
	}
	
	/**
	 * 
	 * @param ResponseInterface $response
	 * @return JsonDecodeException
	 */
	protected function setResponse(ResponseInterface $response)
	{
		$this->response = $response;
		return $this;
	}
	
	/**
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @return JsonDecodeException
	 */
	public static function JsonDecodeFailed(RequestInterface $request, ResponseInterface $response)
	{
		$e = new self('Json decoding failed', $response->getStatusCode());
		$e->setRequest($request)
		  ->setResponse($response);
		return $e;
	}
}