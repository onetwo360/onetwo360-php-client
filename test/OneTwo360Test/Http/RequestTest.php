<?php

/*
 * Copyright (c) 2014, Flaming Code
 */

namespace OneTwo360Test\Http;

use PHPUnit_Framework_TestCase as TestCase;

use ReflectionClass;

use OneTwo360\Http\Request;

/**
 * RequestTest
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Flaming Code
 * @link http://github.com/FlamingCode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class RequestTest extends TestCase
{
	public function testDefaultValues()
	{
		$request = new Request;
		
		$this->assertSame(Request::METHOD_GET, $request->getMethod());
		$this->assertNull($request->getUri());
		$this->assertSame(array(), $request->getQueryParams());
		$this->assertSame(array(), $request->getPostParams());
	}
	
	public function testSettersAndGetters()
	{
		$request = new Request;

		$uri = 'http://someurl.invalid/test/1';
		$queryParams = array(
			'q' => 'Some search value',
			'order' => 'title',
			'direction' => 'asc',
			'multi-value' => array(
				'first',
				'second'
			)
		);
		$postParams = array(
			'title' => 'Oh my dayum!',
			'description' => 'Dayum, dayum, DAYUM!!',
			'tags' => array(
				'We\'re going H.A.M.',
				'Get yourself that double cheese burger'
			)
		);
		
		$request->setMethod(Request::METHOD_POST);
		$request->setUri($uri);
		$request->setQueryParams($queryParams);
		$request->setPostParams($postParams);
		
		$this->assertSame(Request::METHOD_POST, $request->getMethod());
		$this->assertSame($uri, $request->getUri());
		$this->assertSame($queryParams, $request->getQueryParams());
		$this->assertFalse($request->getQueryParam('Unknown-param', false));
		$this->assertSame($queryParams['q'], $request->getQueryParam('q'));
		$this->assertSame($queryParams['multi-value'], $request->getQueryParam('multi-value'));
		$this->assertSame($postParams, $request->getPostParams());
		$this->assertFalse($request->getPostParam('Unknown-param', false));
		$this->assertSame($postParams['title'], $request->getPostParam('title'));
		$this->assertSame($postParams['tags'], $request->getPostParam('tags'));
	}
	
	public function testBooleanConvenienceMethods()
	{
		$request = new Request;
		
		$refl = new ReflectionClass($request);
		
		$classConstants = $refl->getConstants();
		
		// Only the constants for HTTP methods
		$httpMethods = array();
		$convenienceMethods = array();
		foreach ($classConstants as $const => $val) {
			if (0 === strpos($const, 'METHOD_')) {
				$httpMethods[$const] = $val;
				
				$method = 'is' . ucfirst(strtolower($val));
				if (!method_exists($request, $method)) {
					$this->fail(sprintf('No such method: %s', $method));
					break;
				}
				$convenienceMethods[$const] = $method;
			}
		}
		unset($classConstants);
		unset($refl);
		
		foreach ($httpMethods as $const => $httpMethod) {
			$request->setMethod($httpMethod);
			
			foreach ($convenienceMethods as $methodKey => $method)
				$this->assertEquals($methodKey === $const, $request->{$method}());
		}
	}
	
	public function testCanAddAndReplaceQueryParams()
	{
		$request = new Request;
		
		$request->addQueryParam('Test01', 'Test01 Value');
		
		$this->assertSame(array('Test01' => 'Test01 Value'), $request->getQueryParams());
		
		$request->addQueryParam('Test01', 'New Test01 Value', true);

		$this->assertSame(array('Test01' => 'New Test01 Value'), $request->getQueryParams());
		
		// Reset object
		$request = new Request;
		
		$queryParams = array(
			'q' => 'Some search value',
			'multi-value' => array(
				'first',
				'second',
				'third'
			)
		);
		
		foreach ($queryParams as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $innerValue)
					$request->addQueryParam($key, $innerValue, false);
				continue;
			}
			$request->addQueryParam($key, $value, false);
		}
		
		$this->assertSame($queryParams, $request->getQueryParams());
	}
	
	public function testCanAddAndReplacePostParams()
	{
		$request = new Request;
		
		$request->addPostParam('Test01', 'Test01 Value');
		
		$this->assertSame(array('Test01' => 'Test01 Value'), $request->getPostParams());
		
		$request->addPostParam('Test01', 'New Test01 Value', true);

		$this->assertSame(array('Test01' => 'New Test01 Value'), $request->getPostParams());
		
		// Reset object
		$request = new Request;
		
		$postParams = array(
			'title' => 'Oh my dayum!',
			'tags' => array(
				'We\'re going H.A.M.',
				'Get yourself that double cheese burger',
				'One more for the road'
			)
		);
		
		foreach ($postParams as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $innerValue)
					$request->addPostParam($key, $innerValue, false);
				continue;
			}
			$request->addPostParam($key, $value, false);
		}
		
		$this->assertSame($postParams, $request->getPostParams());
	}
	
	public function testRequestLineRendering()
	{
		$request = new Request;
		
		$uri = 'http://someurl.invalid/test/1';
		
		$request->setMethod(Request::METHOD_GET);
		$request->setVersion(Request::VERSION_11);
		$request->setUri($uri);
		
		$this->assertSame(
			Request::METHOD_GET . ' ' . $uri . ' HTTP/' . Request::VERSION_11,
			$request->renderRequestLine()
		);
	}
	
	public function testRequestRendering()
	{
		$request = new Request;
		
		$uri = 'http://someurl.invalid/test/1';
		
		$headers = array(
			'Header01' => 'Header01 Value',
			'Header02' => array(
				'Header02 Value A',
				'Header02 Value B'
			)
		);
		
		$request->setMethod(Request::METHOD_GET);
		$request->setVersion(Request::VERSION_11);
		$request->setUri($uri);
		$request->setHeaders($headers);
		$request->setContent('Test01');
		
		$expectedString = Request::METHOD_GET . ' ' . $uri . ' HTTP/' . Request::VERSION_11 . "\r\n" .
		                  "Header01: Header01 Value\r\nHeader02: Header02 Value A\r\nHeader02: Header02 Value B\r\n" .
		                  "\r\nTest01";
		
		$this->assertSame($expectedString, $request->toString());
		
		$this->assertSame($expectedString, (string) $request);
	}
	
	public function testThrowsExceptionFromBadMethodValue()
	{
		$request = new Request;
		
		$this->setExpectedException(
			'OneTwo360\Http\Exception\InvalidArgumentException',
			'Invalid HTTP method passed'
		);
		
		$request->setMethod('YOUR_MAMMA');
	}
}