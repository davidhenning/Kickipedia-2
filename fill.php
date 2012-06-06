<?php

require_once(__DIR__ . '/bootstrap.php');

use Kickipedia2\Models\EntryDocument;

for($i = 0; $i < 10000; $i++) {
	$entry = new EntryDocument();
	$entry->setProperty('type', rand(1,4));
	$entry->setProperty('user', 1);
	$entry->setProperty('name', "Victim {$i}");
	$entry->setProperty('reason', 'Troll');
	$entry->setProperty('ip', '127.0.0.1');
	$entry->save();
}
