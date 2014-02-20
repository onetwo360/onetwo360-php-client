<?php

/*
 * Copyright (c) 2014, Flaming Code
 */

namespace OneTwo360Test\Client;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

use OneTwo360\Client\Token;
use OneTwo360\Client\Client;

/**
 * ClientTest
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Flaming Code
 * @link http://github.com/FlamingCode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class ClientTest extends TestCase
{
	/**
	 * 
	 * @return MockObject
	 */
	protected function getMockHttpClient()
	{
		$httpClientMock = $this->getMock('OneTwo360\Http\Client', array('send'));
		return $httpClientMock;
	}
	
	public function testIfTestIsPossible()
	{
		$this->assertTrue(true);
	}
}