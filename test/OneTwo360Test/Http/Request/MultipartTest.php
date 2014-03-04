<?php

/*
 * Copyright (c) 2014, Flaming Code
 */

namespace OneTwo360Test\Http\Request;

use PHPUnit_Framework_TestCase as TestCase;

use OneTwo360\Http\Request\Multipart;
use OneTwo360\Http\Request\Part;

/**
 * MultipartTest
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Flaming Code
 * @link http://github.com/FlamingCode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class MultipartTest extends TestCase
{
	public function testDefaultValues()
	{
		$multipartRequest = new Multipart;
		
		$headers = $multipartRequest->getHeaders();
		
		$this->assertArrayHasKey('Content-Type', $headers);
		$this->assertSame(Multipart::ENC_MULTIPART_MIXED, $headers['Content-Type']);
		
		$this->assertNotEmpty($multipartRequest->getBoundary());
	}
	
	public function testConstructorArguments()
	{
		$multipartRequest = new Multipart(Multipart::ENC_MULTIPART_RELATED);
		
		$headers = $multipartRequest->getHeaders();
		
		$this->assertArrayHasKey('Content-Type', $headers);
		$this->assertSame(Multipart::ENC_MULTIPART_RELATED, $headers['Content-Type']);
	}
	
	public function testGettersAndSetters()
	{
		$multipartRequest = new Multipart;
		
		$parts = array(
			new Part('part01'),
			new Part('part02'),
			new Part('part03'),
		);
		
		$multipartRequest->setParts($parts);
		
		$this->assertSame($parts, $multipartRequest->getParts());
		
		$boundry = $multipartRequest->getBoundary();
		
		$expectedContent = "\r\n--{$boundry}\r\nContent-Type: text/plain\r\n\r\npart01" .
		                   "\r\n--{$boundry}\r\nContent-Type: text/plain\r\n\r\npart02" .
		                   "\r\n--{$boundry}\r\nContent-Type: text/plain\r\n\r\npart03" .
		                   "\r\n--{$boundry}--";
		
		$this->assertSame($expectedContent, $multipartRequest->getContent());
		
		$multipartRequest->resetParts();
		
		$this->assertSame(array(), $multipartRequest->getParts());
		
		foreach ($parts as $part)
			$multipartRequest->addPart($part);
		
		$this->assertSame($parts, $multipartRequest->getParts());
		
		$expectedContent = "\r\n--{$boundry}\r\nContent-Type: text/plain\r\n\r\npart01" .
		                   "\r\n--{$boundry}\r\nContent-Type: text/plain\r\n\r\npart02" .
		                   "\r\n--{$boundry}\r\nContent-Type: text/plain\r\n\r\npart03" .
		                   "\r\n--{$boundry}--";
		
		$this->assertSame($expectedContent, $multipartRequest->getContent());
	}
}