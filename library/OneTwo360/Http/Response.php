<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Http;

/**
 * Response
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 */
class Response extends Message implements ResponseInterface
{
	/**
	 * Array of status codes reason-texts
	 *
	 * @var array
	 */
	protected $messageTemplates = array(
		self::STATUS_CONTINUE => 'Continue',
		self::STATUS_SWITCHING_PROTOCOLS => 'Switching Protocols',
		self::STATUS_PROCESSING => 'Processing',
		
		self::STATUS_OK => 'OK',
		self::STATUS_CREATED => 'Created',
		self::STATUS_ACCEPTED => 'Accepted',
		self::STATUS_NONAUTHORATIVE_INFORMATION => 'Non-Authoritative Information',
		self::STATUS_NO_CONTENT => 'No Content',
		self::STATUS_RESET_CONTENT => 'Reset Content',
		self::STATUS_PARTIAL_CONTENT => 'Partial Content',
		self::STATUS_MULTISTATUS => 'Multi-status',
		self::STATUS_ALREADY_REPORTED => 'Already Reported',
		
		self::STATUS_MULTIPLE_CHOICES => 'Multiple Choices',
		self::STATUS_MOVED_PERMANENTLY => 'Moved Permanently',
		self::STATUS_FOUND => 'Found',
		self::STATUS_SEE_OTHER => 'See Other',
		self::STATUS_NOT_MODIFIED => 'Not Modified',
		self::STATUS_USE_PROXY => 'Use Proxy',
		self::STATUS_SWITCH_PROXY => 'Switch Proxy', // Deprecated
		self::STATUS_TEMPORARY_REDIRECT => 'Temporary Redirect',
		
		self::STATUS_BAD_REQUEST => 'Bad Request',
		self::STATUS_UNAUTHORIZED => 'Unauthorized',
		self::STATUS_PAYMENT_REQUIRED => 'Payment Required',
		self::STATUS_FORBIDDEN => 'Forbidden',
		self::STATUS_NOT_FOUND => 'Not Found',
		self::STATUS_METHOD_NOT_ALLOWED => 'Method Not Allowed',
		self::STATUS_NOT_ACCEPTABLE => 'Not Acceptable',
		self::STATUS_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
		self::STATUS_REQUEST_TIMEOUT => 'Request Time-out',
		self::STATUS_CONFLICT => 'Conflict',
		self::STATUS_GONE => 'Gone',
		self::STATUS_LENGTH_REQUIRED => 'Length Required',
		self::STATUS_PRECONDITION_FAILED => 'Precondition Failed',
		self::STATUS_REQUEST_ENTITY_TOO_LARGE => 'Request Entity Too Large',
		self::STATUS_REQUESTURI_TOO_LARGE => 'Request-URI Too Large',
		self::STATUS_UNSUPORTED_MEDIA_TYPE => 'Unsupported Media Type',
		self::STATUS_REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested range not satisfiable',
		self::STATUS_EXPECTATION_FAILED => 'Expectation Failed',
		self::STATUS_IM_A_TEAPOT => 'I\'m a teapot',
		self::STATUS_UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
		self::STATUS_LOCKED => 'Locked',
		self::STATUS_FAILED_DEPENDENCY => 'Failed Dependency',
		self::STATUS_UNORDERED_COLLECTION => 'Unordered Collection',
		self::STATUS_UPGRADE_REQUIRED => 'Upgrade Required',
		self::STATUS_PRECONDITION_REQUIRED => 'Precondition Required',
		self::STATUS_TOO_MANY_REQUESTS => 'Too Many Requests',
		self::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
		
		self::STATUS_INTERNAL_SERVER_ERROR => 'Internal Server Error',
		self::STATUS_NOT_IMPLEMENTED => 'Not Implemented',
		self::STATUS_BAD_GATEWAY => 'Bad Gateway',
		self::STATUS_SERVICE_UNAVAILABLE => 'Service Unavailable',
		self::STATUS_GATEWAY_TIMEOUT => 'Gateway Time-out',
		self::STATUS_HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version not supported',
		self::STATUS_VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
		self::STATUS_INSUFFICIENT_STORAGE => 'Insufficient Storage',
		self::STATUS_LOOP_DETECTED => 'Loop Detected',
		self::STATUS_NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required',
	);
	
	/**
	 * The status code of the HTTP Response
	 *
	 * @var int
	 */
	protected $statusCode = self::STATUS_OK;
	
	/**
	 * The human friendly reason text
	 *
	 * @var string|null
	 */
	protected $message = null;
	
	public static function fromString($string)
	{
		$lines = explode("\r\n", $string);
		if (!is_array($lines) || count($lines) == 1) {
			$lines = explode("\n", $string);
		}

		$firstLine = array_shift($lines);

		$regex = '/^HTTP\/(?P<version>1\.[01]) (?P<status>\d{3})(?:[ ]+(?P<reason>.*))?$/';
		$matches = array();
		if (!preg_match($regex, $firstLine, $matches)) {
			throw new Exception\InvalidArgumentException(
			'A valid response status line was not found in the provided string'
			);
		}

		$response = new static();
		
		$response->setVersion($matches['version']);
		$response->setStatusCode($matches['status']);
		$response->setMessage((isset($matches['reason']) ? $matches['reason'] : ''));

		if (count($lines) == 0) {
			return $response;
		}

		$isHeader = true;
		$headers = $content = array();

		while ($lines) {
			$nextLine = array_shift($lines);

			if ($isHeader && $nextLine == '') {
				$isHeader = false;
				continue;
			}
			if ($isHeader) {
				$headers[] = $nextLine;
			} else {
				$content[] = $nextLine;
			}
		}

		if ($headers) {
			$response->setHeaders(implode("\r\n", $headers));
		}

		if ($content) {
			$response->setContent(implode("\r\n", $content));
		}

		return $response;
	}

	/**
	 * 
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}
	
	/**
	 * 
	 * @param int $code
	 * @return Response
	 * @throws Exception\InvalidArgumentException
	 */
	public function setStatusCode($code)
	{
		if (!is_numeric($code) || !in_array($code, array_keys($this->messageTemplates))) {
			throw new Exception\InvalidArgumentException(sprintf(
				'$code must be a valid HTTP status code. Got: "%s"',
				is_scalar($code) ? $code : gettype($code)
			));
		}
		$this->statusCode = (int) $code;
		return $this;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getMessage()
	{
		if (null === $this->message)
			return $this->messageTemplates[$this->statusCode];
		return $this->message;
	}
	
	/**
	 * 
	 * @param string $message
	 * @return Response
	 */
	public function setMessage($message)
	{
		$this->message = trim((string) $message);
		return $this;
	}
	
	/**
	 * 
	 * @return string|null
	 */
	public function getBody()
	{
		return $this->getContent();
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isServerError()
	{
		$code = $this->getStatusCode();
		return self::STATUS_INTERNAL_SERVER_ERROR <= $code && 600 > $code;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isClientError()
	{
		$code = $this->getStatusCode();
		return self::STATUS_BAD_REQUEST <= $code && self::STATUS_INTERNAL_SERVER_ERROR > $code;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isRedirect()
	{
		$code = $this->getStatusCode();
		return self::STATUS_MULTIPLE_CHOICES <= $code && self::STATUS_BAD_REQUEST > $code;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isSuccess()
	{
		$code = $this->getStatusCode();
		return self::STATUS_OK <= $code && self::STATUS_MULTIPLE_CHOICES > $code;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isOk()
	{
		return self::STATUS_OK === $this->getStatusCode();
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isForbidden()
	{
		return self::STATUS_FORBIDDEN === $this->getStatusCode();
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isNotFound()
	{
		return self::STATUS_NOT_FOUND === $this->getStatusCode();
	}
	
	/**
	 * 
	 * @return string
	 */
	public function renderStatusLine()
	{
		return 'HTTP/' . $this->version . ' ' . $this->statusCode . ' ' . $this->message;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function toString()
	{
		$str = $this->renderStatusLine() . "\r\n";
		$str .= $this->renderHeaders();
		$str .= "\r\n";
		$str .= $this->getContent();
		return $str;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}
}