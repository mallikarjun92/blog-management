<?php

	require_once __DIR__ . '/../vendor/autoload.php';
	
	$dbquery = __DIR__ . '/db.sql';
	
	$migration = new \Core\Migrations();
	$migration->migrate($dbquery);
	
	echo "\r\nEnded migration!";