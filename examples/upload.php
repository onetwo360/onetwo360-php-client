<?php
if (!array_key_exists('folderId', $_GET))
	die('No folderId specified! Specify as get param');
$folderId = $_GET['folderId'];
if (!array_key_exists('containerDir', $_GET))
	die('No containerDir specified! Specify as absolute path in get param');
if (!$containerDir = realpath($_GET['containerDir']))
	die('Invalid containerDir provided!');
$containerName = basename($containerDir);

use OneTwo360\Client\Client;

$clientId = 'myclientid';
$tokenCacheFile = '/tmp/ot360_token_cache';

$client = new Client($clientId, $tokenCacheFile);

// Create container in the newly created folder
$data = array(
	'name' => $containerName,
	'tags' => array(
		'I\'m a container',
		'I can store various media assets',
		'Isn\'t that something?'
	),
	'type' => '360',
	'public' => true,
	'allowedHosts' => array(),
	'requestThrottle' => 0,
	'keyFile' => 0,
	'compressionSetting' => array(
		'quality' => 95,
		'blurAmount' => 0.8
	)
);

$response = $client->post('/folders/' . $folderId . '/containers', $data);

//var_dump($response);

// Get id of new container
$containerId = $response['id'];

// Update entire container (PUT)
//$data = array(
//	'id' => $containerId,
//	'name' => $containerName,
//	'tags' => array(
//		'I\'m a container',
//		'I can store various media assets',
//		'Isn\'t that something?',
//		'Just another tag'
//	),
//	'type' => '360',
//	'public' => true,
//	'allowedHosts' => array(
//		'test.com',
//		'127.0.0.1',
//	),
//	'requestThrottle' => 5,
//	'keyFile' => 10,
//	'compressionSetting' => array(
//		'quality' => 100,
//		'blurAmount' => 1.0
//	)
//);
//
//$response = $client->put('/containers/' . $containerId, $data);

//var_dump($response);

// Update container partially (PATCH)
//$data = array(
//	'name' => 'Test2',
//	'tags' => array(
//		'I\'m a container',
//		'I can store all your media assets'
//	),
//	'public' => true,
//	'allowedHosts' => array(
//		'test.com',
//	),
//	'requestThrottle' => 100,
//	'compressionSetting' => array(
//		'quality' => 70,
//		'blurAmount' => 2.5
//	)
//);
//
//$response = $client->patch('/containers/' . $containerId, $data);

//var_dump($response);

// Create files

// Figure out what the filenames are...
$files = glob($containerDir . '/*.jpg');
if (!is_array($files)) {
	die('Error doing glob');
}

// Get the basename (filename)
$filenames = array_map(function ($val) { return basename($val); }, $files);

// Sort them naturally, ie. 1.jpg, 2.jpg, 3.jpg, ... 36.jpg
// instead of 1.jpg, 10.jpg, 11.jpg, ... 9.jpg
sort($filenames, SORT_NATURAL);

$fileIds = array();
foreach ($filenames as $order => $filename) {
	$data = array(
		'order' => $order
	);

	$response = $client->post('/containers/' . $containerId . '/files', $data);
	
	$fileIds[] = $response['id'];
	
	$data = file_get_contents($containerDir . '/' . $filename);
	$response = $client->upload($response['id'], $data, 'image/jpeg');
}

// Update files

// Make an "ordering"
//shuffle($orderArray);

//foreach ($fileIds as $i => $fileId) {
//	$data = array(
//		'order' => $orderArray[$i]
//	);
//
//	$response = $client->patch('/files/' . $fileId, $data);
//	
//	if (!$client->getLastResponse()->isSuccess() || !$response) {
//		var_dump("Error in request", $client->getLastResponse()->getContent());
//	}
//	
//	if ($orderArray[$i] !== $response['order']) {
//		var_dump("Houston, we have a problem...", $orderArray[$i], $response['order']);
//	}
//}
//
//$response = $client->get('/containers/' . $containerId . '/files', array('order' => 'order', 'direction' => 'asc'));

//var_dump($response['_embedded']['files']);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>OneTwo360 API example</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<p><a href="render.php?containerId=<?= $containerId; ?>">Create version of container</a></p>
	</body>
</html>