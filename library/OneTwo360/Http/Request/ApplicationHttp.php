<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http\Request;

use OneTwo360\Http\RequestInterface;

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
	 * @var RequestInterface
	 */
	protected $childRequest;
	
	/**
	 *
	 * @var string
	 */
	protected $contentId = null;

	public function __construct(RequestInterface $childRequest, $contentId = null)
	{
		$this->addHeader('Content-Type', self::ENCTYPE);
		$this->addHeader('Content-Transfer-Encoding', 'binary');
		$this->setChildRequest($childRequest);
		
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
	
	public function getChildRequest()
	{
		return $this->childRequest;
	}
	
	public function setChildRequest(RequestInterface $request)
	{
		$this->childRequest = $request;
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
		return $this->getChildRequest()->toString();
	}
}