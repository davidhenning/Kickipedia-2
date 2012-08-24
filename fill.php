<?php

require_once(__DIR__ . '/bootstrap.php');

use MongoAppKit\Config,
	MongoAppKit\Storage;

use Kickipedia2\Models\EntryDocument;

use Silex\Application;

$oApp = new Application();
$oConfig = new Config();
$oConfig->setBaseDir(realpath(__DIR__));
$oConfig->addConfigFile($oConfig->getConfDir() . '/mongoappkit.json');
$oConfig->addConfigFile($oConfig->getConfDir() . '/kickipedia2.json');
$oStorage = new Storage($oConfig);

$oApp['debug'] = $oConfig->getProperty('DebugMode');
$oApp['config'] = $oConfig;
$oApp['storage'] = $oStorage;

for($i = 0; $i < 10000; $i++) {
	$entry = new EntryDocument($oApp);
	$ip = rand(1, 255).'.'.rand(1, 255).'.'.rand(1,255).'.'.rand(1,255);

	$entry->setProperty('type', rand(1,4));
	$entry->setProperty('user', 1);
	$entry->setProperty('name', "Victim {$i}");
	$entry->setProperty('reason', 'Troll');
	$entry->setProperty('ip', $ip);
	$entry->save();
}
