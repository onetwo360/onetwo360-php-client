<?php

/*
 * Copyright (c) 2014, Flaming Code
 */

namespace OneTwo360Test\Http;

use PHPUnit_Framework_TestCase as TestCase;

use ReflectionClass;

use OneTwo360\Http\Response;

/**
 * ResponseTest
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Flaming Code
 * @link http://github.com/FlamingCode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class ResponseTest extends TestCase
{
	public function testDefaultValues()
	{
		$response = new Response;
		
		$this->assertSame(Response::STATUS_OK, $response->getStatusCode());
		$this->assertSame('OK', $response->getMessage());
		$this->assertTrue($response->isSuccess());
		$this->assertTrue($response->isOk());
	}
	
	public function testGettersAndSetters()
	{
		$response = new Response;
		
		$response->setStatusCode(Response::STATUS_CREATED);
		
		$this->assertSame(Response::STATUS_CREATED, $response->getStatusCode());
		$this->assertSame('Created', $response->getMessage());
		
		$response->setMessage('Test Message');
		$response->setContent('Test Response Content');
		
		$this->assertSame('Test Message', $response->getMessage());
		$this->assertSame('Test Response Content', $response->getBody());
	}
	
	public function testBooleanConvenienceMethods()
	{
		$response = new Response;
		
		$response->setStatusCode(Response::STATUS_OK);
		
		$this->assertTrue($response->isOk());
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isRedirect());
		$this->assertFalse($response->isNotFound());
		$this->assertFalse($response->isForbidden());
		$this->assertFalse($response->isClientError());
		$this->assertFalse($response->isServerError());
		
		$response->setStatusCode(Response::STATUS_CREATED);
		
		$this->assertFalse($response->isOk());
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isRedirect());
		$this->assertFalse($response->isNotFound());
		$this->assertFalse($response->isForbidden());
		$this->assertFalse($response->isClientError());
		$this->assertFalse($response->isServerError());
		
		$response->setStatusCode(Response::STATUS_MOVED_PERMANENTLY);
		
		$this->assertFalse($response->isOk());
		$this->assertFalse($response->isSuccess());
		$this->assertTrue($response->isRedirect());
		$this->assertFalse($response->isNotFound());
		$this->assertFalse($response->isForbidden());
		$this->assertFalse($response->isClientError());
		$this->assertFalse($response->isServerError());
		
		$response->setStatusCode(Response::STATUS_NOT_FOUND);
		
		$this->assertFalse($response->isOk());
		$this->assertFalse($response->isSuccess());
		$this->assertFalse($response->isRedirect());
		$this->assertTrue($response->isNotFound());
		$this->assertFalse($response->isForbidden());
		$this->assertTrue($response->isClientError());
		$this->assertFalse($response->isServerError());
		
		$response->setStatusCode(Response::STATUS_FORBIDDEN);
		
		$this->assertFalse($response->isOk());
		$this->assertFalse($response->isSuccess());
		$this->assertFalse($response->isRedirect());
		$this->assertFalse($response->isNotFound());
		$this->assertTrue($response->isForbidden());
		$this->assertTrue($response->isClientError());
		$this->assertFalse($response->isServerError());
		
		$response->setStatusCode(Response::STATUS_UNAUTHORIZED);
		
		$this->assertFalse($response->isOk());
		$this->assertFalse($response->isSuccess());
		$this->assertFalse($response->isRedirect());
		$this->assertFalse($response->isNotFound());
		$this->assertFalse($response->isForbidden());
		$this->assertTrue($response->isClientError());
		$this->assertFalse($response->isServerError());
		
		$response->setStatusCode(Response::STATUS_INTERNAL_SERVER_ERROR);
		
		$this->assertFalse($response->isOk());
		$this->assertFalse($response->isSuccess());
		$this->assertFalse($response->isRedirect());
		$this->assertFalse($response->isNotFound());
		$this->assertFalse($response->isForbidden());
		$this->assertFalse($response->isClientError());
		$this->assertTrue($response->isServerError());
	}
	
	public function testStatusLineRendering()
	{
		$response = new Response;
		
		$response->setVersion(Response::VERSION_11);
		$response->setStatusCode(Response::STATUS_OK);
		$response->setMessage('OK');
		
		$this->assertSame(
			'HTTP/' . Response::VERSION_11 . ' ' . Response::STATUS_OK . ' OK',
			$response->renderStatusLine()
		);
	}
	
	public function testResponseRendering()
	{
		$response = new Response;
		
		$headers = array(
			'Header01' => 'Header01 Value',
			'Header02' => array(
				'Header02 Value A',
				'Header02 Value B'
			)
		);
		
		$response->setVersion(Response::VERSION_11);
		$response->setStatusCode(Response::STATUS_OK);
		$response->setMessage('OK');
		$response->setHeaders($headers);
		$response->setContent('Test01');
		
		$expectedString = 'HTTP/' . Response::VERSION_11 . ' ' . Response::STATUS_OK . ' OK' . "\r\n" .
		                  "Header01: Header01 Value\r\nHeader02: Header02 Value A\r\nHeader02: Header02 Value B\r\n" .
		                  "\r\nTest01";
		
		$this->assertSame($expectedString, $response->toString());
		
		$this->assertSame($expectedString, (string) $response);
	}
	
	public function testFromStringMethod()
	{
		$expectedResponse = new Response;
		
		$expectedResponse->setVersion(Response::VERSION_11);
		$expectedResponse->setStatusCode(Response::STATUS_OK);
		$expectedResponse->setMessage('OK');
		
		$responseString = 'HTTP/' . Response::VERSION_11 . ' ' . Response::STATUS_OK . ' OK';
		
		$response = Response::fromString($responseString);
		
		$this->assertEquals($expectedResponse, $response);
		
		$expectedResponse = new Response;
		
		$headers = array(
			'Header01' => 'Header01 Value',
			'Header02' => array(
				'Header02 Value A',
				'Header02 Value B'
			)
		);
		
		$expectedResponse->setVersion(Response::VERSION_11);
		$expectedResponse->setStatusCode(Response::STATUS_OK);
		$expectedResponse->setMessage('OK');
		$expectedResponse->setHeaders($headers);
		$expectedResponse->setContent('Test01');
		
		$responseString = 'HTTP/' . Response::VERSION_11 . ' ' . Response::STATUS_OK . ' OK' . "\r\n" .
		                  "Header01: Header01 Value\r\nHeader02: Header02 Value A\r\nHeader02: Header02 Value B\r\n" .
		                  "\r\nTest01";
		
		$response = Response::fromString($responseString);
		
		$this->assertEquals($expectedResponse, $response);
		
		$responseString = 'HTTP/' . Response::VERSION_11 . ' ' . Response::STATUS_OK . ' OK' . "\n" .
		                  "Header01: Header01 Value\nHeader02: Header02 Value A\nHeader02: Header02 Value B\n" .
		                  "\nTest01";
		
		$response = Response::fromString($responseString);
		
		$this->assertEquals($expectedResponse, $response);
	}
	
	public function testThrowsExceptionFromBadStatusLine()
	{
		$responseString = 'HTTP/ I\'m not a correct status line!';
		
		$this->setExpectedException(
			'OneTwo360\Http\Exception\InvalidArgumentException',
			'A valid response status line was not found in the provided string'
		);
		
		Response::fromString($responseString);
	}
	
	public function testThrowsExceptionFromBadStatusCodeValue()
	{
		$response = new Response;
		
		$this->setExpectedException(
			'OneTwo360\Http\Exception\InvalidArgumentException',
			'$code must be a valid HTTP status code. Got: "HI_THERE"'
		);
		
		// Non-numeric value
		$response->setStatusCode('HI_THERE');
		
		$this->setExpectedException(
			'OneTwo360\Http\Exception\InvalidArgumentException',
			'$code must be a valid HTTP status code. Got: "99999"'
		);
		
		// Unknown status code
		$response->setStatusCode(99999);
	}
}