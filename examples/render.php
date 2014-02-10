<?php
if (!array_key_exists('containerId', $_GET))
	die('No containerId specified! Specify as get param');

$containerId = $_GET['containerId'];

use OneTwo360\Client\Client;

$clientId = 'myclientid';
$tokenCacheFile = '/tmp/ot360_token_cache';

$client = new Client($clientId, $tokenCacheFile);

// Create version
$data = array(
	'width' => 400,
	'height' => 400,
	'zoomWidth' => 800,
	'zoomHeight' => 800
);

$response = $client->post('/containers/' . $containerId . '/versions', $data);

//var_dump($response);

// Get id of new container
$versionId = $response['id'];

// Create render token
$response = $client->post('/versions/' . $versionId . '/render');

//var_dump($response);

// Get id of new container
$renderUri = $response['_links']['self']['href'];

// Start rendering of version
$response = $client->put($renderUri);

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
		<p><span id="progress">0</span> % (<span id="status">N/A</span>)</p>
		
		<p><a href="display.php?versionId=<?= $versionId; ?>">Display version</a></p>
		
		<script type="text/javascript" src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
		<script type="text/javascript">
		//<!--
		var progressElm = document.getElementById('progress');
		var statusElm = document.getElementById('status');
		
		var timer = setInterval(function() { timerFunction(); }, 1000);
		
		var timerFunction = function()
		{
			$.get('progress.php?pollUrl=<?= urlencode($renderUri); ?>', {}, function(data) {
				progressElm.innerText = data.progress;
				statusElm.innerText = data.status;
				
				if ('finished' === data.status || 'aborted' === data.status)
					clearInterval(timer);
			});
		}
		//-->
		</script>
	</body>
</html>