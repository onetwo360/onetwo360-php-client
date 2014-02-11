<?php

/*
 * Copyright (c) 2014, Hammerti.me
 */

namespace OneTwo360\Http;

/**
 * DispatchableInterface
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
interface DispatchableInterface
{
	public function dispatch(RequestInterface $request, ResponseInterface $response = null);
}