<?php

require_once(__DIR__ . '/bootstrap.php');

use Kickipedia2\Models\EntryDocument;

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
