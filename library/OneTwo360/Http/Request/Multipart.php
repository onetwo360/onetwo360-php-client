<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http\Request;

use OneTwo360\Http\RequestInterface;
use OneTwo360\Http\Request;

/**
 * Multipart
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class Multipart extends Request
{
	const ENC_MULTIPART_MIXED = 'multipart/mixed';
	const ENC_MULTIPART_RELATED = 'multipart/related';
	
	/**
	 *
	 * @var Request[]
	 */
	protected $parts = array();
	
	/**
	 *
	 * @var string
	 */
	protected $boundary;
	
	public function __construct($enctype = self::ENC_MULTIPART_MIXED)
	{
		$this->addHeader('Content-Type', $enctype);
		$this->boundary = $this->generateBoundary();
	}
	
	protected function generateBoundary()
	{
		return base64_encode(sha1(uniqid('http-boundary', true), true));
	}
	
	public function getBoundary()
	{
		return $this->boundary;
	}

	public function getParts()
	{
		return $this->parts;
	}
	
	public function setParts(array $parts)
	{
		$this->parts = $parts;
		return $this;
	}
	
	public function addPart(RequestInterface $part)
	{
		$this->parts[] = $part;
		return $this;
	}
	
	public function resetParts()
	{
		$this->parts = array();
		return $this;
	}
	
	public function getContent()
	{
		$content = '';
		foreach ($this->parts as $part) {
			// Boundary must be on new line
			$content .= "\r\n--" . $this->getBoundary() . "\r\n";
			$content .= $part->toString();
		}
		// Boundary must be on new line
		$content .= "\r\n--" . $this->getBoundary() . '--';
		return $content;
	}
}