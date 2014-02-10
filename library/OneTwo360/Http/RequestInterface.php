<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http;

/**
 * RequestInterface
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/flamingcode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
interface RequestInterface
{
	const METHOD_OPTIONS = 'OPTIONS';
	const METHOD_GET = 'GET';
	const METHOD_HEAD = 'HEAD';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';
	const METHOD_TRACE = 'TRACE';
	const METHOD_CONNECT = 'CONNECT';
	const METHOD_PATCH = 'PATCH';
	const METHOD_PROPFIND = 'PROPFIND';
	
	public function getMethod();
	public function setMethod($method);
	
	public function getUri();
	public function setUri($uri);
	
	public function getQueryParams();
	public function setQueryParams(array $params);
	public function getQueryParam($name, $default = null);
	public function addQueryParam($name, $value, $replace = true);
	
	public function getPostParams();
	public function setPostParams(array $params);
	public function getPostParam($name, $default = null);
	public function addPostParam($name, $value, $replace = true);
	
	public function toString();
}