<?php

/*
 * Copyright (c) 2014, Flaming Code
 */

namespace OneTwo360Test\Http\Request;

use PHPUnit_Framework_TestCase as TestCase;

use OneTwo360\Http\Request\Part;

/**
 * PartTest
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Flaming Code
 * @link http://github.com/FlamingCode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class PartTest extends TestCase
{
	public function testDefaultValues()
	{
		$part = new Part;
		
		$this->assertSame('', $part->getContent());
		
		$headers = $part->getHeaders();
		
		$this->assertArrayHasKey('Content-Type', $headers);
		$this->assertSame('text/plain', $headers['Content-Type']);
	}
	
	public function testConstructorArguments()
	{
		$part = new Part('{ "im_a_test_value": true }', 'application/json');
		
		$this->assertSame('{ "im_a_test_value": true }', $part->getContent());
		
		$headers = $part->getHeaders();
		
		$this->assertArrayHasKey('Content-Type', $headers);
		$this->assertSame('application/json', $headers['Content-Type']);
	}
	
	public function testRequestRendering()
	{
		$part = new Part('{ "im_a_test_value": true }', 'application/json');
		
		$expectedString = "Content-Type: application/json\r\n" .
		                  "\r\n" . '{ "im_a_test_value": true }';
		
		$this->assertSame($expectedString, $part->toString());
		
		$part->addHeader('Another-Header', 'some_value');
		
		$expectedString = "Content-Type: application/json\r\nAnother-Header: some_value\r\n" .
		                  "\r\n" . '{ "im_a_test_value": true }';
		
		$this->assertSame($expectedString, $part->toString());
	}
}