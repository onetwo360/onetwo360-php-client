<?php

/*
 * Copyright (c) 2014, Hammerti.me
 */

namespace OneTwo360\Http\Client\Adapter;

/**
 * Curl
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class Curl implements AdapterInterface
{
	protected $config = array();
	
	protected $curl = null;
	
	protected $response = null;
	
	public function setOptions(array $options = array())
	{
		foreach ($options as $k => $v) {
			unset($options[$k]);
			$options[str_replace(array('-', '_', ' ', '.'), '', strtolower($k))] = $v;
		}
		$this->config = $options;
		
		return $this;
	}
	
	public function getConfig()
	{
		return $this->config;
	}
	
	public function setCurlOption($option, $value = null)
	{
		if (!isset($this->config['curloptions']))
			$this->config['curloptions'] = array();
		$this->config['curloptions'][$option] = $value;
		return $this;
	}
	
	public function getHandle()
	{
		return $this->curl;
	}
	
	public function connect($port = 80)
	{
		if ($this->curl)
			$this->close();
		
		$this->curl = curl_init();
		
		if (80 !== $port)
			curl_setopt($this->curl, CURLOPT_PORT, (int) $port);
		
		if (isset($this->config['timeout']))
			curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->config['timeout']);

		if (isset($this->config['maxredirects']))
			curl_setopt($this->curl, CURLOPT_MAXREDIRS, $this->config['maxredirects']);
		
		if (!$this->curl) {
			$this->close();
			
			throw new Exception\RuntimeException('Unable to initialize curl');
		}
	}
	
	public function write($method, $uri, $httpVersion = 1.1, array $headers = array(), $body = '')
	{
		if (!$this->curl)
			throw new Exception\RuntimeException('Trying to write without being connected');
		
		curl_setopt($this->curl, CURLOPT_URL, $uri);
		
		$curlMethod = null;
		$curlValue = true;
		switch (strtoupper($method)) {
			case 'GET':
				$curlMethod = CURLOPT_HTTPGET;
				break;
			
			case 'POST':
				$curlMethod = CURLOPT_POST;
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
				break;
			
			case 'PUT':
				$curlMethod = CURLOPT_CUSTOMREQUEST;
				$curlValue = 'PUT';
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
				break;
			
			case 'PATCH':
				$curlMethod = CURLOPT_CUSTOMREQUEST;
				$curlValue = 'PATCH';
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
				break;
			
			case 'DELETE':
				$curlMethod = CURLOPT_CUSTOMREQUEST;
				$curlValue = 'DELETE';
				break;
			
			case 'OPTIONS':
				$curlMethod = CURLOPT_CUSTOMREQUEST;
				$curlValue = 'OPTIONS';
				break;
			
			case 'TRACE':
				$curlMethod = CURLOPT_CUSTOMREQUEST;
				$curlValue = 'TRACE';
				break;
			
			case 'HEAD':
				$curlMethod = CURLOPT_CUSTOMREQUEST;
				$curlValue = 'HEAD';
				break;
			
			default:
				throw new Exception\RuntimeException(sprintf('Method %s currently not supported', $method));
		}
		
		$curlHttp = ($httpVersion == 1.1) ? CURL_HTTP_VERSION_1_1 : CURL_HTTP_VERSION_1_0;

		curl_setopt($this->curl, $curlHttp, true);
		curl_setopt($this->curl, $curlMethod, $curlValue);
		
		curl_setopt($this->curl, CURLOPT_HEADER, true);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headersArrayToCurlHeadersArray($headers));
		
		if (isset($this->config['curloptions'])) {
			foreach ($this->config['curloptions'] as $k => $v)
				curl_setopt($this->curl, $k, $v);
		}
		
		$rawResponse = curl_exec($this->curl);
		
		$request  = curl_getinfo($this->curl, CURLINFO_HEADER_OUT);
		$request .= $body;
		
		$info = curl_getinfo($this->curl);
		if (false === $rawResponse || 0 !== curl_errno($this->curl)) {
			$error = curl_error($this->curl);
			throw new Exception\RuntimeException(sprintf('Request error! CURL error: %s', $error));
		}
		
		$this->response = $rawResponse;
		
		// cURL automatically decodes chunked-messages, this means we have to disallow the OneTwo360\Http\Response to do it again
		if (stripos($this->response, "Transfer-Encoding: chunked\r\n")) {
			$this->response = str_ireplace("Transfer-Encoding: chunked\r\n", '', $this->response);
		}

		// cURL can automatically handle content encoding; prevent double-decoding from occurring
		if (isset($this->config['curloptions'][CURLOPT_ENCODING])
		    && '' == $this->config['curloptions'][CURLOPT_ENCODING]
		    && stripos($this->response, "Content-Encoding: gzip\r\n")) {
			$this->response = str_ireplace("Content-Encoding: gzip\r\n", '', $this->response);
		}
		
		// Eliminate continue response		
		$this->response = trim(preg_replace("|^HTTP/1\.[01] 100 Continue\r\n|mi", '', $this->response));
		
		// cURL automatically handles Proxy rewrites, remove the "HTTP/1.0 200 Connection established" string:
		if (stripos($this->response, "HTTP/1.0 200 Connection established\r\n\r\n") !== false) {
			$this->response = str_ireplace("HTTP/1.0 200 Connection established\r\n\r\n", '', $this->response);
		}
		
		return $request;
	}
	
	public function read()
	{
		return $this->response;
	}
	
	public function close()
	{
		if (is_resource($this->curl))
			curl_close($this->curl);
		$this->curl = null;
	}
	
	/**
	 * 
	 * @param array $headers
	 * @return array
	 */
	protected function headersArrayToCurlHeadersArray(array $headers)
	{
		$curlHeaders = array();
		foreach ($headers as $hKey => $hVal)
			if (is_array($hVal)) {
				foreach ($hVal as $hInnerVal)
					$curlHeaders[] = $hKey . ': ' . $hInnerVal;
			} else
				$curlHeaders[] = $hKey . ': ' . $hVal;
		return $curlHeaders;
	}
}