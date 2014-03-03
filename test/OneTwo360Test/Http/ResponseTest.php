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
}