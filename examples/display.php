<?php
if (!array_key_exists('versionId', $_GET))
	die('No versionId specified! Specify as get param');

$versionId = $_GET['versionId'];

use OneTwo360\Client\Client;

$clientId = 'myclientid';
$tokenCacheFile = '/tmp/ot360_token_cache';

$client = new Client($clientId, $tokenCacheFile);

$embedData = $client->get('/versions/' . $versionId . '/embed-data');

//var_dump($embedData);

$cdnUrl = $embedData['baseUrl'];

$files = $embedData['files'];

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>OneTwo360 API example</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<h1>Images</h1>
		<? foreach ($files as $order => $file): ?>
		<p>
			<img style="margin: 8px; border: 1px dashed #000;"
			     src="<?= $cdnUrl . $file['normal'] ?>"
			     alt="<?= htmlspecialchars($embedData['name'] . ' (' . $order . ' normal)') ?>">
			<img style="margin: 8px; border: 1px dashed #000;"
			     src="<?= $cdnUrl . $file['zoom'] ?>"
			     alt="<?= htmlspecialchars($embedData['name'] . ' (' . $order . ' zoom)') ?>">
		</p>
		<hr>
		<? endforeach; ?>
		
		<h1>Embed data JSON</h1>
		<pre><code><?= json_encode($embedData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?></code></pre>
	</body>
</html>