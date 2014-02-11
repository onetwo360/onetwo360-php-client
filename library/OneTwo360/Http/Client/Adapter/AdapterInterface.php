<?php

/*
 * Copyright (c) 2014, Hammerti.me
 */

namespace OneTwo360\Http\Client\Adapter;

/**
 * AdapterInterface
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
interface AdapterInterface
{
	public function setOptions(array $options = array());
	
	public function connect($port = 80);
	
	public function write($method, $uri, $httpVersion = 1.1, array $headers = array(), $body = '');
	
	public function read();

	public function close();
}