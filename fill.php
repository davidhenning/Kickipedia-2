<?php

require_once(__DIR__ . '/bootstrap.php');

use Kickipedia2\Models\EntryRecord;

for($i = 0; $i < 1000; $i++) {
	$entry = new EntryRecord();
	$entry->setProperty('type', 3);
	$entry->setProperty('user', 1);
	$entry->setProperty('name', "Victim {$i}");
	$entry->setProperty('reason', 'Troll');
	$entry->setProperty('ip', '127.0.0.1');
	$entry->save();
}