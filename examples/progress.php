<?php
if (!array_key_exists('pollUrl', $_GET))
	die('No pollUrl specified! Specify as get param');

use OneTwo360\Client\Client;

$clientId = 'myclientid';
$tokenCacheFile = '/tmp/ot360_token_cache';

$client = new Client($clientId, $tokenCacheFile);

$response = $client->get($_GET['pollUrl']);

header('Content-Type: application/json');
echo json_encode($response);