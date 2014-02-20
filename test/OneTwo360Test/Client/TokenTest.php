<?php

/*
 * Copyright (c) 2014, Flaming Code
 */

namespace OneTwo360Test\Client;

use PHPUnit_Framework_TestCase as TestCase;

use OneTwo360\Client\Token;

/**
 * TokenTest
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Flaming Code
 * @link http://github.com/FlamingCode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class TokenTest extends TestCase
{
	public function testIfTestingIsPossible()
	{
		$token = new Token(array());
		
		$this->assertTrue(is_array($token->toArray()));
		
		//$this->assertTrue(true);
	}
}