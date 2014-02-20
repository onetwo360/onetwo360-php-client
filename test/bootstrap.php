<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

if (!$loader)
	throw new Exception('Unable to get autoloader. Did you install the required dependencies through composer?');