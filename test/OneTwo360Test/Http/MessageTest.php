<?php

/*
 * Copyright (c) 2014, Flaming Code
 */

namespace OneTwo360Test\Http;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * MessageTest
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Flaming Code
 * @link http://github.com/FlamingCode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class MessageTest extends TestCase
{
	public function testGettersAndSetters()
	{
		$message = $this->getMockForAbstractClass('OneTwo360\Http\Message');
		/* @var $message \OneTwo360\Http\Message */
		
		$headers = array(
			'Header01' => 'Header01 Value',
			'Header02' => array(
				'Header02 Value A',
				'Header02 Value B'
			)
		);
		
		$message->setVersion('1.0');
		$message->setHeaders($headers);
		$message->setContent('Test');
		
		$this->assertSame('1.0', $message->getVersion());
		$this->assertSame($headers, $message->getHeaders());
		$this->assertFalse($message->getHeader('Unknown-Header', false));
		$this->assertSame($headers['Header01'], $message->getHeader('Header01'));
		$this->assertSame($headers['Header02'], $message->getHeader('Header02'));
		$this->assertSame('Test', $message->getContent());
	}
	
	public function testCanSetHeadersFromString()
	{
		$message = $this->getMockForAbstractClass('OneTwo360\Http\Message');
		/* @var $message \OneTwo360\Http\Message */
		
		$headersString = "Header01: Header01 Value\r\nHeader02: Header02 Value A\r\nHeader02: Header02 Value B\r\nHeader02: Header02 Value C";
		
		$message->setHeaders($headersString);
		
		$headers = array(
			'Header01' => 'Header01 Value',
			'Header02' => array(
				'Header02 Value A',
				'Header02 Value B',
				'Header02 Value C'
			)
		);
		
		$this->assertSame($headers, $message->getHeaders());
	}
	
	public function testCanAddAndReplaceHeader()
	{
		$message = $this->getMockForAbstractClass('OneTwo360\Http\Message');
		/* @var $message \OneTwo360\Http\Message */
		
		$message->addHeader('Header01', 'Header01 Value');
		
		$this->assertSame(array('Header01' => 'Header01 Value'), $message->getHeaders());
		
		$message->addHeader('Header01', 'New Header01 Value', true);
		
		$this->assertSame(array('Header01' => 'New Header01 Value'), $message->getHeaders());
		
		// Reset headers
		$message->setHeaders(array());
		
		$this->assertSame(array(), $message->getHeaders());
		
		$headers = array(
			'Header01' => 'Header01 Value',
			'Header02' => array(
				'Header02 Value A',
				'Header02 Value B',
				'Header02 Value C'
			)
		);
		
		foreach ($headers as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $innerValue)
					$message->addHeader($key, $innerValue, false);
				continue;
			}
			$message->addHeader($key, $value, false);
		}
		
		$this->assertSame($headers, $message->getHeaders());
	}
	
	public function testHeaderRendering()
	{
		$message = $this->getMockForAbstractClass('OneTwo360\Http\Message');
		/* @var $message \OneTwo360\Http\Message */
		
		$this->assertSame("\r\n", $message->renderHeaders());
		
		$headers = array(
			'Header01' => 'Header01 Value',
			'Header02' => array(
				'Header02 Value A',
				'Header02 Value B'
			)
		);
		
		$message->setHeaders($headers);
		
		$this->assertSame(
			"Header01: Header01 Value\r\nHeader02: Header02 Value A\r\nHeader02: Header02 Value B\r\n",
			$message->renderHeaders()
		);
	}
	
	public function testThrowsExceptionFromBadHeaderValue()
	{
		$message = $this->getMockForAbstractClass('OneTwo360\Http\Message');
		/* @var $message \OneTwo360\Http\Message */
		
		$this->setExpectedException(
			'OneTwo360\Http\Exception\InvalidArgumentException',
			'$headers must either be a string or an array'
		);
		
		// Not a string or an array
		$message->setHeaders(true);
	}
	
	public function testThrowsExceptionFromBadVersionValue()
	{
		$message = $this->getMockForAbstractClass('OneTwo360\Http\Message');
		/* @var $message \OneTwo360\Http\Message */
		
		$this->setExpectedException(
			'OneTwo360\Http\Exception\InvalidArgumentException',
			'Not valid or not supported HTTP version: 99.7'
		);
		
		// Obviously wrong version value
		$message->setVersion('99.7');
	}
}