<?php

/*
 * Copyright (c) 2014, Hammerti.me
 */

namespace OneTwo360\Http\Response;

use OneTwo360\Http\ResponseInterface;
use OneTwo360\Http\Response;

/**
 * Multipart
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class Multipart extends Response
{
	const ENC_MULTIPART_MIXED = 'multipart/mixed';
	const ENC_MULTIPART_RELATED = 'multipart/related';
	
	/**
	 *
	 * @var Response[]
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
	}
	
	public function setBoundary($boundary)
	{
		$this->boundary = (string) $boundary;
		return $this;
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
	
	public function addPart(ResponseInterface $part)
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
	
	public static function fromString($string)
	{
		$response = parent::fromString($string);
		
		$contentType = array_map(
			function($val) { return trim($val); },
			explode(';', $response->getHeader('Content-Type'))
		);
		
		$tmp = explode('=', $contentType[1], 2);
		$boundary = trim($tmp[1], '"');
		unset($tmp);
		unset($contentType);
		
		$response->setBoundary($boundary);
		
		$rawBody = $response->content;
		$partsBody = substr($rawBody, strlen("\r\n--$boundary\r\n"),
		                    strpos($rawBody, "\r\n--$boundary--") - strlen("\r\n--$boundary--"));
		unset($response->content);
		
		$responses = explode("\r\n--$boundary\r\n", $partsBody);
		foreach ($responses as $partResponse) {
			$tmp = explode("\r\n\r\n", $partResponse, 2);
			$partHeaders = array();
			$headers = explode("\r\n", $tmp[0]);
			$partResponse = $tmp[1];
			unset($tmp);
			foreach ($headers as $header) {
				$headerExplode = explode(':', $header, 2);
				$partHeaders[$headerExplode[0]] = trim($headerExplode[1]);
			}
			unset($headers);
			
			if (array_key_exists('Content-Type', $partHeaders) &&
			    'application/http' === $partHeaders['Content-Type']) {
				$contentId = null;
				if (array_key_exists('Content-ID', $partHeaders))
					$contentId = substr($partHeaders['Content-ID'], 1, -1);

				$response->addPart(new ApplicationHttp(Response::fromString($partResponse), $contentId));
				continue;
			}
			
			$part = new Part($partResponse);
			$part->setHeaders($partHeaders);
			$response->addPart($part);
		}
		
		return $response;
	}
}