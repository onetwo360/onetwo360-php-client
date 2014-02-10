<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http;

use OneTwo360\Http\Request\Multipart as MultipartRequest;
use OneTwo360\Http\Response\Multipart as MultipartResponse;

/**
 * Client
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Client implements DispatchableInterface
{
	const ENC_URLENCODED = 'application/x-www-form-urlencoded';
	const ENC_FORMDATA = 'multipart/form-data';
	
	protected $request;

	protected $response;
	
	protected $adapter;
	
	protected $encType = '';
	
	protected $redirectCount = 0;
	
	protected $config = array(
		'useragent' => 'OneTwo360 http client',
		'adapter' => 'OneTwo360\Http\Client\Adapter\Curl',
		'timeout' => 10,
		'maxredirects' => 5,
		'httpversion' => Request::VERSION_11
	);
	
	public function __construct($uri = null, array $options = null)
	{
		if (null !== $uri)
			$this->setUri($uri);
		if (null !== $options)
			$this->setOptions($options);
	}
	
	public function setOptions(array $options)
	{
		foreach ($options as $k => $v)
			$this->config[str_replace(array('-', '_', ' ', '.'), '', strtolower($k))] = $v;

		// Pass configuration options to the adapter if it exists
		if ($this->adapter instanceof Client\Adapter\AdapterInterface)
			$this->adapter->setOptions($options);
		return $this;
	}
	
	public function dispatch(RequestInterface $request, ResponseInterface $response = null)
	{
		$response = $this->send($request);
		return $response;
	}
	
	public function send(RequestInterface $request)
	{
		if (null !== $request)
			$this->setRequest($request);
		
		$this->redirectCounter = 0;

		$adapter = $this->getAdapter();
		$adapter->connect(80);

		$uri = $this->getUri();

		$queryParams = $this->getRequest()->getQueryParams();
		if (!empty($queryParams)) {
			$uri .= $this->array2QueryString($queryParams);
		}

		$method = $this->getMethod();
		$body = $this->prepareBody();
		$headers = $this->prepareHeaders($body, $uri);
		$version = $this->getRequest()->getVersion();
		
		$requestString = $adapter->write($method, $uri, $version, $headers, $body);
		$response = $adapter->read();
		$adapter->close();
		
		$this->response = Response::fromString($response);
		if (preg_match('/^multipart\/(related|mixed)/', $this->response->getHeader('Content-Type')))
			$this->response = MultipartResponse::fromString($response);
		
		return $this->response;
	}
	
	public function getUri()
	{
		return $this->getRequest()->getUri();
	}
	
	public function setUri($uri)
	{
		$this->getRequest()->setUri($uri);
		return $this;
	}
	
	public function getRequest()
	{
		if (empty($this->request))
			$this->request = new Request();
		return $this->request;
	}
	
	public function setRequest(RequestInterface $request)
	{
		$this->request = $request;
		return $this;
	}
	
	public function getResponse()
	{
		if (empty($this->response))
			$this->response = new Response();
		return $this->response;
	}
	
	public function setResponse(ResponseInterface $response)
	{
		$this->response = $response;
		return $this;
	}
	
	public function getAdapter()
	{
		if (empty($this->adapter))
			$this->setAdapter($this->config['adapter']);
		return $this->adapter;
	}
	
	public function setAdapter($adapter)
	{
		if (is_string($adapter)) {
			if (!class_exists($adapter))
				throw new Exception\InvalidArgumentException('Provided adapter class does\'t exsist');
			$adapter = new $adapter;
		}
		
		if (!$adapter instanceof Client\Adapter\AdapterInterface) {
			throw new Exception\InvalidArgumentException('Invalid adapter class provided');
		}
		$this->adapter = $adapter;
		$config = $this->config;
		unset($config['adapter']);
		$this->adapter->setOptions($config);
		return $this;
	}
	
	public function setRawBody($body)
	{
		$this->getRequest()->setContent($body);
		return $this;
	}
	
	public function getMethod()
	{
		return $this->getRequest()->getMethod();
	}
	
	public function setMethod($method)
	{
		$method = $this->getRequest()->setMethod($method)->getMethod();

		if (($method == Request::METHOD_POST || $method == Request::METHOD_PUT ||
		     $method == Request::METHOD_DELETE || $method == Request::METHOD_PATCH)
		     && empty($this->encType)) {
			$this->setEncType(self::ENC_URLENCODED);
		}

		return $this;
	}
	
	public function getEncType()
	{
		return $this->encType;
	}
	
	public function setEncType($encType, $boundary = null)
	{
		if (!empty($boundary)) {
			$encType .= "; boundary={$boundary}";
		}
		$this->encType = $encType;
		return $this;
	}
	
	public function getRedirectCount()
	{
		return $this->redirectCount;
	}
	
	public function setPostParams(array $postParam)
	{
		$this->getRequest()->setPostParams($postParam);
		return $this;
	}
	
	public function setQueryParams(array $queryParams)
	{
		$this->getRequest()->setQueryParams($queryParams);
		return $this;
	}
	
	/**
	 * 
	 * @param array $array
	 * @return string
	 */
	protected function array2QueryString(array $array)
	{
		if (count($array))
			return '?' . http_build_query($array, null, '&');
		return '';
	}
	
	protected function prepareBody()
	{
		$request = $this->getRequest();
		if ($request->isTrace())
			return '';
		
		if ($request instanceof MultipartRequest) {
			$this->setEncType($request->getHeader('Content-Type'), $request->getBoundary());
		}
		
		$rawBody = $request->getContent();
		if (!empty($rawBody)) {
			return $rawBody;
		}
		
		$body = '';
		
		if ($encType = $request->getHeader('Content-Type')) {
			$this->setEncType($encType);
		}
		
		if (stripos($this->getEncType(), self::ENC_URLENCODED) === 0) {
			// Encode body as application/x-www-form-urlencoded
			$body = http_build_query($request->getPostParams());
		}
		
		return $body;
	}
	
	protected function prepareHeaders($body, $uri)
	{
		$headers = $this->getRequest()->getHeaders();
		
		if ($this->config['httpversion'] == Request::VERSION_11) {
			$matches = array();
			if (!preg_match('|^http(s)?://(?<host>[^/]*)|', $uri, $matches))
				throw new Exception\RuntimeException('Invalid uri: ' . $uri);
			
			$headers['Host'] = $matches['host'];
		}
		
		if (!in_array('Connection', $headers)) {
			$headers['Connection'] = 'close';
		}
		
		/*if (!in_array('Accept-Encoding', $headers)) {
			if (function_exists('gzinflate')) {
				$headers['Accept-Encoding'] = 'gzip, deflate';
			} else {
				$headers['Accept-Encoding'] = 'identity';
			}
		}*/

		if (!in_array('User-Agent', $headers) && isset($this->config['useragent'])) {
			$headers['User-Agent'] = $this->config['useragent'];
		}
		
		// Content-type
		$encType = $this->getEncType();
		if (!empty($encType)) {
			$headers['Content-Type'] = $encType;
		}
		
		$headers['Content-MD5'] = base64_encode(md5($body, true));
		$headers['Content-Length'] = strlen($body);
		
		return $headers;
	}
}
