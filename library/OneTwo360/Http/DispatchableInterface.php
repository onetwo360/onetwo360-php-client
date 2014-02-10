<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http;

/**
 * DispatchableInterface
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link http://github.com/flamingcode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
interface DispatchableInterface
{
	public function dispatch(RequestInterface $request, ResponseInterface $response = null);
}