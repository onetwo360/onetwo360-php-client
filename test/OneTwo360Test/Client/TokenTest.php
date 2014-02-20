<?php

/*
 * Copyright (c) 2014, Flaming Code
 */

namespace OneTwo360Test\Client;

use PHPUnit_Framework_TestCase as TestCase;

use OneTwo360\Client\Token;

use DateTime;

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
	public function testDefaultValues()
	{
		$token = new Token();
		
		$this->assertInstanceOf('\DateTime', $token->getCreatedAt());
		$this->assertNull($token->getAccessToken());
		$this->assertSame(0, $token->getExpiresIn());
		$this->assertSame(Token::TYPE_BEARER, $token->getTokenType());
	}
	
	public function testTokenConstructor()
	{
		$token = new Token(array(
			'accessToken' => '123',
			'expiresIn' => 10,
			'tokenType' => 'SomeType'
		));
		
		$this->assertInstanceOf('\DateTime', $token->getCreatedAt());
		$this->assertSame('123', $token->getAccessToken());
		$this->assertSame(10, $token->getExpiresIn());
		$this->assertSame('SomeType', $token->getTokenType());
	}
	
	public function testSettersAndGetters()
	{
		$token = new Token();
		
		$token->setAccessToken('123');
		$token->setCreatedAt('2014-02-20 10:00:00');
		$token->setExpiresIn(10);
		$token->setTokenType('SomeType');
		
		$this->assertInstanceOf('\DateTime', $token->getCreatedAt());
		$this->assertSame('2014-02-20 10:00:00', $token->getCreatedAt()->format('Y-m-d H:i:s'));
		$this->assertSame('123', $token->getAccessToken());
		$this->assertSame(10, $token->getExpiresIn());
		$this->assertSame('SomeType', $token->getTokenType());
		
		$theTime = new DateTime('2014-02-20 10:00:00');
		$token->setCreatedAt($theTime);
		
		$this->assertInstanceOf('\DateTime', $token->getCreatedAt());
		$this->assertSame($theTime, $token->getCreatedAt());
	}
	
	public function testToArray()
	{
		$token = new Token();
		
		$theTime = new DateTime('2014-02-20 10:00:00');
		
		$token->setAccessToken('123');
		$token->setCreatedAt($theTime);
		$token->setExpiresIn(10);
		$token->setTokenType('SomeType');
		
		$values = $token->toArray();
		
		$this->assertTrue(is_array($values));
		$this->assertArrayHasKey('accessToken', $values);
		$this->assertArrayHasKey('createdAt', $values);
		$this->assertArrayHasKey('expiresIn', $values);
		$this->assertArrayHasKey('tokenType', $values);
		
		$this->assertEquals(array(
			'accessToken' => '123',
			'createdAt' => $theTime,
			'expiresIn' => 10,
			'tokenType' => 'SomeType'
		), $values);
	}
	
	public function testCanDetectValidAndInvalidTokens()
	{
		// Automatically has current date and time through constructor
		$token = new Token(array(
			'accessToken' => '123',
			'expiresIn' => 3600, // An hour
			'tokenType' => 'SomeType'
		));
		
		$this->assertTrue($token->isValid());
		
		// Date in the past
		$token->setCreatedAt('2004-01-01 10:00:00');
		
		$this->assertFalse($token->isValid());
	}
}