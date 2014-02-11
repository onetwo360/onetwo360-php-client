<?php

/*
 * Copyright (c) 2014, Hammerti.me
 */

namespace OneTwo360\Client;

use DateTime;

/**
 * Token
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class Token
{
	const TYPE_BEARER = 'Bearer';

	/**
	 *
	 * @var string
	 */
	protected $accessToken;
	
	/**
	 *
	 * @var DateTime
	 */
	protected $createdAt;
	
	/**
	 *
	 * @var int
	 */
	protected $expiresIn = 0;
	
	/**
	 *
	 * @var string
	 */
	protected $tokenType = self::TYPE_BEARER;
	
	/**
	 * 
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->createdAt = new DateTime;
		$methods = get_class_methods($this);
		foreach ($data as $k => $v) {
			$method = 'set' . ucfirst($k);
			if (in_array($method, $methods))
				$this->$method($v);
		}
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getAccessToken()
	{
		return $this->accessToken;
	}
	
	/**
	 * 
	 * @param string $token
	 * @return Token
	 */
	public function setAccessToken($token)
	{
		$this->accessToken = (string) $token;
		return $this;
	}
	
	/**
	 * 
	 * @return DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	
	/**
	 * 
	 * @param string|DateTime $createdAt
	 * @return Token
	 */
	public function setCreatedAt($createdAt)
	{
		if (is_string($createdAt))
			$this->createdAt = new DateTime($createdAt);
		elseif ($createdAt instanceof DateTime)
			$this->createdAt = $createdAt;
		return $this;
	}
	
	/**
	 * 
	 * @return int
	 */
	public function getExpiresIn()
	{
		return $this->expiresIn;
	}
	
	/**
	 * 
	 * @param int $expiresIn
	 * @return Token
	 */
	public function setExpiresIn($expiresIn)
	{
		$this->expiresIn = (int) $expiresIn;
		return $this;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getTokenType()
	{
		return $this->tokenType;
	}
	
	/**
	 * 
	 * @param string $type
	 * @return Token
	 */
	public function setTokenType($type)
	{
		$this->tokenType = (string) $type;
		return $this;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isValid()
	{
		return ($this->createdAt->getTimestamp() + $this->expiresIn) > time();
	}
	
	/**
	 * 
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'accessToken' => $this->accessToken,
			'createdAt' => $this->createdAt,
			'expiresIn' => $this->expiresIn,
			'tokenType' => $this->tokenType
		);
	}
}