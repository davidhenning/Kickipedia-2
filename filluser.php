<?php

require_once(__DIR__ . '/bootstrap.php');

use Kickipedia2\Models\UserDocument;

$entry = new UserDocument();
$entry->setProperty('name', 'MadCat');
$entry->setPassword('test');
$entry->setProperty('token', md5("MadCat:Kickipedia2:test"));
$entry->setProperty('email', 'madcat.me@gmail.com');
$entry->save();

$entry = new UserDocument();
$entry->setProperty('name', 'kicki');
$entry->setPassword('pedia');
$entry->setProperty('token', md5("kicki:Kickipedia2:pedia"));
$entry->setProperty('email', 'madcat.me@gmail.com');
$entry->save();