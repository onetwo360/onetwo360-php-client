<?php

/*
 * Copyright (c) 2014, Hammerti.me
 */

namespace OneTwo360\Http\Response;

use OneTwo360\Http\Response;

/**
 * Part
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class Part extends Response
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
	
	public static function fromString($string)
	{
		$lines = explode("\r\n", $string);
		if (!is_array($lines) || count($lines) == 1) {
			$lines = explode("\n", $string);
		}
		
		$response = new static();

		if (count($lines) == 0) {
			return $response;
		}

		// If has status line, skip it
		$regex = '/^HTTP\/(?P<version>1\.[01]) (?P<status>\d{3})(?:[ ]+(?P<reason>.*))?$/';
		$matches = array();
		if (!preg_match($regex, $lines[0], $matches)) {
			array_shift($lines);
		}

		$isHeader = true;
		$headers = $content = array();

		while ($lines) {
			$nextLine = array_shift($lines);

			if ($isHeader && $nextLine == '') {
				$isHeader = false;
				continue;
			}
			if ($isHeader) {
				$headers[] = $nextLine;
			} else {
				$content[] = $nextLine;
			}
		}

		if ($headers) {
			$response->setHeaders(implode("\r\n", $headers));
		}

		if ($content) {
			$response->setContent(implode("\r\n", $content));
		}

		return $response;
	}
}