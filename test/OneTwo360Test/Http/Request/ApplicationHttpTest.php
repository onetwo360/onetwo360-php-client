<?php

/*
 * Copyright (c) 2014, Flaming Code
 */

namespace OneTwo360Test\Http\Request;

use PHPUnit_Framework_TestCase as TestCase;

use OneTwo360\Http\Request\Multipart;
use OneTwo360\Http\Request\ApplicationHttp;
use OneTwo360\Http\Request;

/**
 * ApplicationHttpTest
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Flaming Code
 * @link http://github.com/FlamingCode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class ApplicationHttpTest extends TestCase
{
	public function testDefaultValues()
	{
		$request = new Request;
		
		$applicationHttp = new ApplicationHttp($request);
		
		$this->assertNull($applicationHttp->getContentId());
		$this->assertSame($request, $applicationHttp->getChildRequest());
		
		$this->assertSame($request->toString(), $applicationHttp->getContent());
		
		$headers = $applicationHttp->getHeaders();
		
		$this->assertArrayNotHasKey('Content-ID', $headers);
		
		$this->assertArrayHasKey('Content-Type', $headers);
		$this->assertSame(ApplicationHttp::ENCTYPE, $headers['Content-Type']);
		
		$this->assertArrayHasKey('Content-Transfer-Encoding', $headers);
		$this->assertSame('binary', $headers['Content-Transfer-Encoding']);
	}
}