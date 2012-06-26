<?php

require_once(__DIR__ . '/bootstrap.php');

use MongoAppKit\Config,
	MongoAppKit\Storage;

use Kickipedia2\Models\EntryDocument;

$oConfig = new Config();
$oConfig->addConfigFile('mongoappkit.json');
$oConfig->addConfigFile('kickipedia2.json');
$oStorage = new Storage($oConfig);
$oDatabase = $oStorage->getDatabase();

for($i = 0; $i < 10000; $i++) {
	$entry = new EntryDocument($oDatabase, $oConfig);
	$ip = rand(1, 255).'.'.rand(1, 255).'.'.rand(1,255).'.'.rand(1,255);

	$entry->setProperty('type', rand(1,4));
	$entry->setProperty('user', 1);
	$entry->setProperty('name', "Victim {$i}");
	$entry->setProperty('reason', 'Troll');
	$entry->setProperty('ip', $ip);
	$entry->save();
}
