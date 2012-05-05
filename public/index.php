<?php

$time = microtime(true);

require_once(__DIR__ . '/../bootstrap.php');

use Kickipedia2\Controllers\EntryActions;

$entryActions = new EntryActions();

run();

echo sprintf("%01.6f", microtime(true) - $time).'s, max memory usage: '.(memory_get_peak_usage() / 1024 / 1024).' MB';