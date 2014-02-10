<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http\Response\Exception;

use OneTwo360\Http\Exception\RuntimeException as HttpRuntimeException;

/**
 * RuntimeException
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2013, Flaming Code
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/GPL-2.0 GPL-2.0
 */
class RuntimeException extends HttpRuntimeException implements ExceptionInterface
{}