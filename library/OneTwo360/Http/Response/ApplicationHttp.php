<?php

/*
 * Copyright (c) 2014, Hammerti.me
 */

namespace OneTwo360\Http\Response;

use OneTwo360\Http\ResponseInterface;

/**
 * ApplicationHttp
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class ApplicationHttp extends Part
{
	const ENCTYPE = 'application/http';
	
	/**
	 *
	 * @var ResponseInterface
	 */
	protected $childResponse;
	
	/**
	 *
	 * @var string
	 */
	protected $contentId = null;

	public function __construct(ResponseInterface $childResponse, $contentId = null)
	{
		$this->addHeader('Content-Type', self::ENCTYPE);
		$this->addHeader('Content-Transfer-Encoding', 'binary');
		$this->setChildResponse($childResponse);
		
		if (null !== $contentId)
			$this->setContentId($contentId);
	}
	
	public function getContentId()
	{
		return $this->contentId;
	}
	
	public function setContentId($id)
	{
		$this->contentId = (string) $id;
		return $this;
	}
	
	public function getChildResponse()
	{
		return $this->childResponse;
	}
	
	public function setChildResponse(ResponseInterface $response)
	{
		$this->childResponse = $response;
		return $this;
	}
	
	public function renderHeaders()
	{
		$contentId = $this->getContentId();
		if (!empty($contentId))
			$this->addHeader('Content-ID', '<' . $contentId . '>');
		return parent::renderHeaders();
	}
	
	public function getContent()
	{
		return $this->getChildResponse()->toString();
	}
}