<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http;

/**
 * MessageInterface
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link http://github.com/flamingcode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
interface MessageInterface
{
	public function getHeaders();
	public function getHeader($name, $default = false);
	public function setHeaders($headers);
	public function addHeader($name, $value, $replace = true);
	public function renderHeaders();
	
	public function getContent();
	public function setContent($content);
}