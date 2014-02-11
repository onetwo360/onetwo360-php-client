<?php
// Since we are "inside" the examples dir we need to go up to get the autoload file from composer
require '../../../autoload.php';

use OneTwo360\Client\Client;

$clientId = 'myclientid';
$tokenCacheFile = '/tmp/ot360_token_cache';

$client = new Client($clientId, $tokenCacheFile);

$userId = 1337;

// Create new folder
$data = array(
	'name' => 'A Folder',
	'description' => 'My very first Folder',
	'tags' => array(
		'test',
		'test2',
		'test'
	),
	'viewerSetting' => array(
		'settings' => array(
			'zoomEnabled' => true,
			'animateOnLoad' => false,
			'animationDuration' => 200,
			'rotateBounce' => false,
			'sensitivity' => 100
		),
		'logoEnabled' => false,
		'logoMarkup' => null,
		'logoLinkUrl' => null
	)
);

$response = $client->post('/users/' . $userId . '/folders', $data);

//var_dump($response);

// Get id of new folder
$folderId = $response['id'];

// Update entire folder (PUT)
//$data = array(
//	'id' => $folderId,
//	'name' => 'A Folder',
//	'description' => 'I have a new description! :-)',
//	'tags' => array(
//		'A brand new tag!',
//		'test2'
//	),
//	'viewerSetting' => array(
//		'settings' => array(
//			'zoomEnabled' => true,
//			'animateOnLoad' => false,
//			'animationDuration' => 200,
//			'rotateBounce' => false,
//			'sensitivity' => 100
//		),
//		'logoEnabled' => false,
//		'logoMarkup' => null,
//		'logoLinkUrl' => null
//	)
//);
//
//$response = $client->put('/folders/' . $folderId, $data);

//var_dump($response);

// Update folder partially (PATCH)
//$data = array(
//	'name' => 'New name for folder',
//	'tags' => array(
//		'test',
//		'I\'m a new tag!',
//		'test2'
//	)
//);
//
//$response = $client->patch('/folders/' . $folderId, $data);

//var_dump($response);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>OneTwo360 API example</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<p><a href="upload.php?folderId=<?= $folderId; ?>">Upload to folder</a></p>
	</body>
</html>