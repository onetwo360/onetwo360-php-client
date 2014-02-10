<?php

/*
 * Copyright (c) 2013, Flaming Code
 */

namespace OneTwo360\Client;

use OneTwo360\Http\RequestInterface;
use OneTwo360\Http\Request;
use OneTwo360\Http\Request\Multipart as MultipartRequest;
use OneTwo360\Http\Request\ApplicationHttp as ApplicationHttpRequest;
use OneTwo360\Http\ResponseInterface;
use OneTwo360\Http\Client as HttpClient;

/**
 * Client
 *
 * @author Flemming Andersen <flemming@flamingcode.com>
 * @copyright (c) 2014, Hammerti.me
 * @link https://github.com/onetwo360/onetwo360-php-client for the canonical source repository
 * @license http://opensource.org/licenses/MIT MIT
 * 
 * Folder methods
 * @method array getFolders(array $params) Get all folders
 * @method array getUserFolders($userId, array $params) Get all folders for a specific user
 * @method array addFolder($userId, $data) Add new folder for a user
 * @method array getFolder($folderId) Get a specific folder
 * @method array updateFolder($folderId, $data) Update a folder
 * @method bool deleteFolder($folderId) Delete a folder
 * @method bool isFolderSharedToUser($folderId, $userId) Check if a folder is shared with a user
 * @method bool shareFolderToUser($folderId, $userId) Share a folder with a user
 * @method bool unshareFolderToUser($folderId, $userId) Unshare a folder with a user
 * @method array folderSharedToUsers($folderId, array $params) Gets a list of all users a folder has been shared to
 * 
 * Container methods
 * @method array getContainers(array $params) Get all containers
 * @method array getFolderContainers($folderId, array $params) Get all containers for a specific folder
 * @method array addContainer($folderId, $data) Add new container to a folder
 * @method array getContainer($containerId) Get a specific container
 * @method array updateContainer($containerId, $data) Update a container
 * @method bool deleteContainer($containerId) Delete a container
 * 
 * Container Metadata methods
 * @method array getContainerMetadata(array $params) Get all container metadata
 * @method array getContainerContainerMetadata($containerId, array $params) Get all metadata for a specific container
 * @method array addContainerMetadataEntry($containerId, $data) Add new metadata for a container
 * @method array getContainerMetadataEntry($containerMetadataId) Get a specific container
 * @method array updateContainerMetadataEntry($containerMetadataId, $data) Update a container
 * @method bool deleteContainerMetadataEntry($containerMetadataId) Delete a container
 * 
 * File methods
 * @method array getFiles(array $params) Get all files
 * @method array getContainerFiles($containerId, array $params) Get all files for a specific container
 * @method array addFile($containerId, $data) Add new file to a container
 * @method array getFile($fileId) Get a specific file
 * @method array updateFile($fileId, $data) Update a file
 * @method bool deleteFile($fileId) Delete a file
 * 
 * Interaction Point methods
 * @method array getInteractionPoints(array $params) Get all interactionpoints
 * @method array getFileInteractionPoints($fileId, array $params) Get all interaction points for a specific file
 * @method array addInteractionPoint($fileId, $data) Add new interaction point to a file
 * @method array getInteractionPoint($interactionPointId) Get a specific interaction point
 * @method array updateInteractionPoint($interactionPointId, $data) Update a interaction point
 * @method bool deleteInteractionPoint($interactionPointId) Delete a interaction point
 * 
 * Version methods
 * @method array getVersions(array $params) Get all versions
 * @method array getContainerVersions($containerId, array $params) Get all versions for a specific container
 * @method array addVersion($containerId, $data) Add new version of a container
 * @method array getVersion($versionId) Get a specific version
 * @method array updateVersion($versionId, $data) Update a version
 * @method bool deleteVersion($versionId) Delete a version
 * @method array getVersionEmbedData($versionId) Get embed data for a specific version
 * @method array requestRenderToken($versionId) Get a new render token for a specific version
 * @method array initiateVersionRendering($versionId, $token) Starts the rendering process of a version
 * @method array getRenderingProgress($versionId, $token) Get the status and progress of a version rendering process
 */
class Client
{
	/**
	 *
	 * @var string
	 */
	protected $baseUri = 'http://api.onetwo360.com';
	
	/**
	 *
	 * @var HttpClient
	 */
	protected $httpClient;
	
	/**
	 *
	 * @var string
	 */
	protected $clientId;
	
	/**
	 *
	 * @var Token|null
	 */
	protected $token = null;
	
	/**
	 *
	 * @var string
	 */
	protected $tokenCacheFile = null;
	
	/**
	 *
	 * @var RequestInterface|null
	 */
	protected $lastRequest = null;
	
	/**
	 *
	 * @var ResponseInterface|null
	 */
	protected $lastResponse = null;
	
	/**
	 *
	 * @var array
	 */
	protected static $methodMap = array(
		'getfolders' => array(
			'method' => 'GET',
			'identifierCount' => 0,
			'uri' => '/folders',
		),
		'getuserfolders' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/users/%d/folders',
		),
		'addfolder' => array(
			'method' => 'POST',
			'identifierCount' => 1,
			'uri' => '/users/%d/folders',
		),
		'getfolder' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/folders/%d',
		),
		'updatefolder' => array(
			'method' => 'PATCH',
			'identifierCount' => 1,
			'uri' => '/folders/%d',
		),
		'deletefolder' => array(
			'method' => 'DELETE',
			'identifierCount' => 1,
			'uri' => '/folders/%d',
		),
		'isfoldersharedtouser' => array(
			'method' => 'GET',
			'identifierCount' => 2,
			'uri' => '/folders/%d/shared-to/%d',
		),
		'sharefoldertouser' => array(
			'method' => 'PUT',
			'identifierCount' => 2,
			'uri' => '/folders/%d/shared-to/%d',
		),
		'unsharefoldertouser' => array(
			'method' => 'DELETE',
			'identifierCount' => 2,
			'uri' => '/folders/%d/shared-to/%d',
		),
		'foldersharedtousers' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/folders/%d/shared-to',
		),
		
		'getcontainers' => array(
			'method' => 'GET',
			'identifierCount' => 0,
			'uri' => '/containers',
		),
		'getfoldercontainers' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/folders/%d/containers',
		),
		'addcontainer' => array(
			'method' => 'POST',
			'identifierCount' => 1,
			'uri' => '/folders/%d/containers',
		),
		'getcontainer' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/containers/%d',
		),
		'updatecontainer' => array(
			'method' => 'PATCH',
			'identifierCount' => 1,
			'uri' => '/containers/%d',
		),
		'deletecontainer' => array(
			'method' => 'DELETE',
			'identifierCount' => 1,
			'uri' => '/containers/%d',
		),
		
		'getcontainermetadata' => array(
			'method' => 'GET',
			'identifierCount' => 0,
			'uri' => '/container-metadata',
		),
		'getcontainercontainermetadata' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/containers/%d/containers',
		),
		'addcontainermetadataentry' => array(
			'method' => 'POST',
			'identifierCount' => 1,
			'uri' => '/containers/%d/container-metadata',
		),
		'getcontainermetadataentry' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/container-metadata/%d',
		),
		'updatecontainermetadataentry' => array(
			'method' => 'PATCH',
			'identifierCount' => 1,
			'uri' => '/container-metadata/%d',
		),
		'deletecontainermetadataentry' => array(
			'method' => 'DELETE',
			'identifierCount' => 1,
			'uri' => '/container-metadata/%d',
		),
		
		'getfiles' => array(
			'method' => 'GET',
			'identifierCount' => 0,
			'uri' => '/files',
		),
		'getcontainerfiles' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/containers/%d/files',
		),
		'addfile' => array(
			'method' => 'POST',
			'identifierCount' => 1,
			'uri' => '/containers/%d/files',
		),
		'getfile' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/files/%d',
		),
		'updatefile' => array(
			'method' => 'PATCH',
			'identifierCount' => 1,
			'uri' => '/files/%d',
		),
		'deletefile' => array(
			'method' => 'DELETE',
			'identifierCount' => 1,
			'uri' => '/files/%d',
		),
		
		'getinteractionpoints' => array(
			'method' => 'GET',
			'identifierCount' => 0,
			'uri' => '/interaction-points',
		),
		'getfileinteractionpoints' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/files/%d/interaction-points',
		),
		'addinteractionpoint' => array(
			'method' => 'POST',
			'identifierCount' => 1,
			'uri' => '/files/%d/interaction-points',
		),
		'getinteractionpoint' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/interaction-points/%d',
		),
		'updateinteractionpoint' => array(
			'method' => 'PATCH',
			'identifierCount' => 1,
			'uri' => '/interaction-points/%d',
		),
		'deleteinteractionpoint' => array(
			'method' => 'DELETE',
			'identifierCount' => 1,
			'uri' => '/interaction-points/%d',
		),
		
		'getversions' => array(
			'method' => 'GET',
			'identifierCount' => 0,
			'uri' => '/versions',
		),
		'getcontainerversions' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/containers/%d/versions',
		),
		'addversion' => array(
			'method' => 'POST',
			'identifierCount' => 1,
			'uri' => '/containers/%d/versions',
		),
		'getversion' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/versions/%d',
		),
		'updateversion' => array(
			'method' => 'PATCH',
			'identifierCount' => 1,
			'uri' => '/versions/%d',
		),
		'deleteversion' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/versions/%d',
		),
		'getversionembeddata' => array(
			'method' => 'GET',
			'identifierCount' => 1,
			'uri' => '/versions/%d/embed-data',
		),
		'requestrendertoken' => array(
			'method' => 'POST',
			'identifierCount' => 1,
			'uri' => '/versions/%d/render',
		),
		'initiateversionrendering' => array(
			'method' => 'PUT',
			'identifierCount' => 2,
			'uri' => '/versions/%d/render/%s',
		),
		'getrenderingprogress' => array(
			'method' => 'GET',
			'identifierCount' => 2,
			'uri' => '/versions/%d/render/%s',
		),
	);
	
	/**
	 * 
	 * @param string $clientId
	 * @param string $tokenCacheFile
	 */
	public function __construct($clientId = null, $tokenCacheFile = null)
	{
		if (null !== $clientId)
			$this->setClientId($clientId);
		if (null !== $tokenCacheFile)
			$this->setTokenCacheFile($tokenCacheFile);
		
		$this->httpClient = new HttpClient;
		$this->httpClient->setOptions(array(
			'user_agent' => 'OneTwo360 API Client',
		));
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getBaseUri()
	{
		return $this->baseUri;
	}
	
	/**
	 * 
	 * @param string $uri
	 * @return Client
	 */
	public function setBaseUri($uri)
	{
		$this->baseUri = (string) $uri;
		return $this;
	}
	
	/**
	 * 
	 * @return HttpClient
	 */
	public function getHttpClient()
	{
		return $this->httpClient;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getClientId()
	{
		return $this->clientId;
	}
	
	/**
	 * 
	 * @param string $clientId
	 * @return Client
	 */
	public function setClientId($clientId)
	{
		$this->clientId = (string) $clientId;
		return $this;
	}
	
	/**
	 * 
	 * @return bool
	 */
	protected function readTokenFromCache()
	{
		if (null === $this->tokenCacheFile || !file_exists($this->tokenCacheFile))
			return false;
		
		$token = new Token(unserialize(file_get_contents($this->tokenCacheFile)));
		
		if (!$token->isValid())
			return false;
		
		$this->token = $token;
		
		return true;
	}
	
	/**
	 * 
	 * @param Token $token
	 * @return bool
	 * @throws Exception\RuntimeException
	 */
	protected function writeTokenToCache(Token $token)
	{
		if (null === $this->tokenCacheFile)
			return false;
		
		if (false === file_put_contents($this->tokenCacheFile, serialize($token->toArray())))
			throw new Exception\RuntimeException('Writing to token cache file failed');
		
		return true;
	}
	
	/**
	 * 
	 * @return Token
	 */
	protected function aquireToken()
	{
		throw new Exception\RuntimeException('Method not implemented');
	}
	
	/**
	 * Resets the token
	 * 
	 * @return void
	 */
	public function resetToken()
	{
		if (file_exists($this->tokenCacheFile))
			unlink($this->tokenCacheFile);
		$this->token = null;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getTokenCacheFile()
	{
		return $this->tokenCacheFile;
	}
	
	/**
	 * 
	 * @param string $file
	 * @return Client
	 */
	public function setTokenCacheFile($file)
	{
		$this->tokenCacheFile = (string) $file;
		return $this;
	}
	
	/**
	 * Gets the last request object sent by the api client
	 * 
	 * @return RequestInterface
	 */
	public function getLastRequest()
	{
		return $this->lastRequest;
	}
	
	/**
	 * Gets the last response object received by the api client
	 * 
	 * @return ResponseInterface
	 */
	public function getLastResponse()
	{
		return $this->lastResponse;
	}
	
	/**
	 * 
	 * @param string $uri BaseUri will be prepended if not already present in the uri
	 * @param string $method
	 * @param array|string $data
	 * @param string $contentType
	 * @return array|bool Array if Json decoding is possible. True on No Content response
	 * @throws Exception\JsonDecodeException Throws if Json decoding not possible
	 */
	public function call($uri, $method = 'GET', $data = null, $contentType = 'application/json')
	{
		$this->lastRequest = null;
		$this->lastResponse = null;
		
		$request = $this->createRequest($uri, $method, $data, $contentType);
		$this->addAuthorizationHeader($request);
		$this->lastRequest = $request;
		
		$response = $this->sendRequest($request);
		$this->lastResponse = $response;
		
		if ($response->getStatusCode() === 401) { // Unauthorized
			// Try with a new token
			$this->addAuthorizationHeader($request, true);
			
			$response = $this->sendRequest($request);
			if ($response->getStatusCode() === 401) // Unauthorized again
				throw new Exception\RuntimeException('Got unauthorized after request');
			
			$this->lastRequest = $request;
			$this->lastResponse = $response;
		}
		
		if ($response->getStatusCode() === 204) // No Content, just return true
			return true;
		
		$responseData = json_decode($response->getContent(), true);
		if (empty($responseData)) // If we can't decode the response content
			throw Exception\JsonDecodeException::JsonDecodeFailed($request, $response);
		
		return $responseData;
	}
	
	/**
	 * 
	 * @param string $uri
	 * @param string $method
	 * @param string|array $data
	 * @param string $contentType
	 * @return Request
	 */
	protected function createRequest($uri, $method = 'GET', $data = null, $contentType = 'application/json')
	{
		$request = new Request();
		
		// If URI includes protocol and host we don't need to prepend it
		if (0 === strpos($uri, $this->getBaseUri()))
			$request->setUri($uri);
		else
			$request->setUri($this->getBaseUri() . $uri);
		
		$request->setMethod(strtoupper($method))
			->addHeader('Accept', 'application/json');
		
		if ('GET' === $request->getMethod() && is_array($data)) {
			$request->setQueryParams($data);
			$data = null;
		}
		
		if (null !== $data) {
			if (is_array($data))
				$data = json_encode($data);
			
			$request->addHeader('Content-Type', $contentType)
			        ->setContent($data);
		}
		
		return $request;
	}
	
	/**
	 * 
	 * @param RequestInterface $request
	 * @param bool $forceNewToken
	 * @return RequestInterface
	 */
	protected function addAuthorizationHeader(RequestInterface $request, $forceNewToken = false)
	{
		if ($forceNewToken || !$this->readTokenFromCache()) {
			$this->token = $this->aquireToken();
			$this->writeTokenToCache($this->token);
		}
		
		$request->addHeader('Authorization', $this->token->getTokenType() . ' ' . $this->token->getAccessToken()); 
		
		return $request;
	}

	/**
	 * 
	 * @param RequestInterface $request
	 * @return ResponseInterface
	 * @throws Exception\RuntimeException
	 */
	protected function sendRequest(RequestInterface $request)
	{
		return $this->getHttpClient()->send($request);
	}
	
	/**
	 * Convenience method
	 * 
	 * @param string $uri
	 * @param array $queryParams
	 * @return array|null
	 */
	public function get($uri, array $queryParams = null)
	{
		return $this->call($uri, 'GET', $queryParams);
	}
	
	/**
	 * Convenience method
	 * 
	 * @param string $uri
	 * @param array|string $data
	 * @return array|null
	 */
	public function post($uri, $data = null)
	{
		return $this->call($uri, 'POST', $data);
	}
	
	/**
	 * Convenience method
	 * 
	 * @param string $uri
	 * @param array|string $data
	 * @return array|null
	 */
	public function put($uri, $data = null)
	{
		return $this->call($uri, 'PUT', $data);
	}
	
	/**
	 * Convenience method
	 * 
	 * @param string $uri
	 * @param array|string $data
	 * @return array|null
	 */
	public function patch($uri, $data = null)
	{
		return $this->call($uri, 'PATCH', $data);
	}
	
	/**
	 * Convenience method
	 * 
	 * @param string $uri
	 * @return bool
	 */
	public function delete($uri)
	{
		$this->call($uri, 'DELETE');
		return $this->getLastResponse()->isSuccess();
	}
	
	/**
	 * Upload data to a file
	 * 
	 * @param int $fileId The id of the file to upload to
	 * @param mixed $data The raw file data
	 * @param string $contentType The content-type for the data
	 * @return array|null
	 */
	public function upload($fileId, $data, $contentType = 'application/octet-stream')
	{
		return $this->call('/files/' . $fileId . '/upload', 'POST', $data, $contentType);
	}
	
	/**
	 * Magic method for enabling calls like "$client->getContainer($containerId)"
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return array|bool
	 * @throws Exception\BadMethodCallException
	 * @throws Exception\InvalidArgumentException
	 */
	public function __call($name, $arguments)
	{
		$name = strtolower($name);
		if (!array_key_exists($name, self::$methodMap))
			throw new Exception\BadMethodCallException('Call to undefined method');
		
		$method = self::$methodMap[$name];
		$uri = $method['uri'];
		$data = null;
		
		// Do we need to supply any identifiers?
		if (0 < $method['identifierCount']) {
			if (count($arguments) < $method['identifierCount'])
				throw new Exception\InvalidArgumentException('Missing identifiers');
			
			$identifiers = array_splice($arguments, 0, $method['identifierCount']);
			
			$uri = vsprintf($uri, $identifiers);
		}
		
		// Are there any params or data?
		if (array_key_exists(0, $arguments))
			$data = array_shift($arguments);
		
		return $this->call($uri, $method['method'], $data);
	}
	
	/**
	 * 
	 * @param array $requests Array of RequestInterface or array
	 * @return ResponseInterface
	 * @throws Exception\InvalidArgumentException
	 * @throws Exception\RuntimeException
	 */
	public function batchCall(array $requests)
	{
		$this->lastRequest = null;
		$this->lastResponse = null;
		
		$batchRequest = new MultipartRequest(MultipartRequest::ENC_MULTIPART_MIXED);
		$batchRequest->setUri($this->getBaseUri() . '/batch')
		             ->setMethod('POST');
		
		$batchId = sha1(uniqid('batch', true));
		
		$count = 1;
		foreach ($requests as $request) {
			if (is_array($request)) {
				$options = array_merge(array(
					'uri' => '/',
					'method' => 'GET',
					'data' => null,
					'contentType' => 'application/json',
				), $request);
				
				$request = $this->createRequest($options['uri'], $options['method'],
				                                $options['data'], $options['contentType']);
			} elseif (!$request instanceof RequestInterface)
				throw new Exception\InvalidArgumentException('Invalid request provided in $requests. Must be an array of either array or instance of OneTwo360\\Http\\RequestInterface');
			
			if ($request->isPut() || $request->isPatch() || $request->isPost())
				$request->addHeader('Content-Length', strlen($request->getContent()));
			
			$batchRequest->addPart(new ApplicationHttpRequest($request, $batchId . '+' . $count));
			$count++;
		}
		
		$this->addAuthorizationHeader($batchRequest);
		$this->lastRequest = $batchRequest;
		
		$response = $this->sendRequest($batchRequest);
		$this->lastResponse = $response;
		
		if ($response->getStatusCode() === 401) { // Unauthorized
			// Try with a new token
			$this->addAuthorizationHeader($batchRequest, true);
			
			$response = $this->sendRequest($batchRequest);
			if ($response->getStatusCode() === 401) // Unauthorized again
				throw new Exception\RuntimeException('Got unauthorized after request');
			
			$this->lastRequest = $batchRequest;
			$this->lastResponse = $response;
		}
		
		return $response;
	}
}
