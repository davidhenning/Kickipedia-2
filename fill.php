<?php

require_once(__DIR__ . '/bootstrap.php');

use MongoAppKit\Config;

use Kickipedia2\Models\EntryDocument;

Config::getInstance()->addConfigFile('mongoappkit.json');
Config::getInstance()->addConfigFile('kickipedia2.json');

for($i = 0; $i < 10000; $i++) {
	$entry = new EntryDocument();
	$ip = rand(1, 255).'.'.rand(1, 255).'.'.rand(1,255).'.'.rand(1,255);

	$entry->setProperty('type', rand(1,4));
	$entry->setProperty('user', 1);
	$entry->setProperty('name', "Victim {$i}");
	$entry->setProperty('reason', 'Troll');
	$entry->setProperty('ip', $ip);
	$entry->save();
}
