<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http\Request;

use OneTwo360\Http\Request;

/**
 * Part
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Part extends Request
{
	public function __construct($content = '', $contentType = 'text/plain')
	{
		$this->addHeader('Content-Type', $contentType);
		$this->setContent($content);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function toString()
	{
		$str = $this->renderHeaders();
		$str .= "\r\n";
		$str .= $this->getContent();
		return $str;
	}
}