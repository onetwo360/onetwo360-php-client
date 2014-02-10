<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http;

/**
 * ResponseInterface
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link http://github.com/flamingcode/my-repo for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
interface ResponseInterface
{
	/**#@+
	 * Informational - 1XX
	 */
	const STATUS_CONTINUE = 100;
	const STATUS_SWITCHING_PROTOCOLS = 101;
	const STATUS_PROCESSING = 102;
	
	/**#@+
	 * Successful - 2XX
	 */
	const STATUS_OK = 200;
	const STATUS_CREATED = 201;
	const STATUS_ACCEPTED = 202;
	const STATUS_NONAUTHORATIVE_INFORMATION = 203;
	const STATUS_NO_CONTENT = 204;
	const STATUS_RESET_CONTENT = 205;
	const STATUS_PARTIAL_CONTENT = 206;
	const STATUS_MULTISTATUS = 207;
	const STATUS_ALREADY_REPORTED = 208;

	/**#@+
	 * Redirection - 3XX
	 */
	const STATUS_MULTIPLE_CHOICES = 300;
	const STATUS_MOVED_PERMANENTLY = 301;
	const STATUS_FOUND = 302;
	const STATUS_SEE_OTHER = 303;
	const STATUS_NOT_MODIFIED = 304;
	const STATUS_USE_PROXY = 305;
	const STATUS_SWITCH_PROXY = 306; // Deprecated
	const STATUS_TEMPORARY_REDIRECT = 307;

	/**#@+
	 * Client Error - 4XX
	 */
	const STATUS_BAD_REQUEST = 400;
	const STATUS_UNAUTHORIZED = 401;
	const STATUS_PAYMENT_REQUIRED = 402;
	const STATUS_FORBIDDEN = 403;
	const STATUS_NOT_FOUND = 404;
	const STATUS_METHOD_NOT_ALLOWED = 405;
	const STATUS_NOT_ACCEPTABLE = 406;
	const STATUS_PROXY_AUTHENTICATION_REQUIRED = 407;
	const STATUS_REQUEST_TIMEOUT = 408;
	const STATUS_CONFLICT = 409;
	const STATUS_GONE = 410;
	const STATUS_LENGTH_REQUIRED = 411;
	const STATUS_PRECONDITION_FAILED = 412;
	const STATUS_REQUEST_ENTITY_TOO_LARGE = 413;
	const STATUS_REQUESTURI_TOO_LARGE = 414;
	const STATUS_UNSUPORTED_MEDIA_TYPE = 415;
	const STATUS_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	const STATUS_EXPECTATION_FAILED = 417;
	const STATUS_IM_A_TEAPOT = 418;
	const STATUS_UNPROCESSABLE_ENTITY = 422;
	const STATUS_LOCKED = 423;
	const STATUS_FAILED_DEPENDENCY = 424;
	const STATUS_UNORDERED_COLLECTION = 425;
	const STATUS_UPGRADE_REQUIRED = 426;
	const STATUS_PRECONDITION_REQUIRED = 428;
	const STATUS_TOO_MANY_REQUESTS = 429;
	const STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

	/**#@+
	 * Server Error - 5XX
	 */
	const STATUS_INTERNAL_SERVER_ERROR = 500;
	const STATUS_NOT_IMPLEMENTED = 501;
	const STATUS_BAD_GATEWAY = 502;
	const STATUS_SERVICE_UNAVAILABLE = 503;
	const STATUS_GATEWAY_TIMEOUT = 504;
	const STATUS_HTTP_VERSION_NOT_SUPPORTED = 505;
	const STATUS_VARIANT_ALSO_NEGOTIATES = 506;
	const STATUS_INSUFFICIENT_STORAGE = 507;
	const STATUS_LOOP_DETECTED = 508;
	const STATUS_NETWORK_AUTHENTICATION_REQUIRED = 511;

	public function getStatusCode();
	public function setStatusCode($code);
	
	public function getMessage();
	public function setMessage($message);
	
	public function getBody();
	
	public function isClientError();
	public function isServerError();
	public function isSuccess();
	public function isOk();
	public function isRedirect();
	public function isNotFound();
	public function isForbidden();
	
	public function toString();
}