<?php

require_once(__DIR__ . '/bootstrap.php');

use MongoAppKit\Config,
	MongoAppKit\Storage;

use Kickipedia2\Models\UserDocument;

$oConfig = new Config();
$oConfig->addConfigFile('mongoappkit.json');
$oConfig->addConfigFile('kickipedia2.json');
$oStorage = new Storage($oConfig);
$oDatabase = $oStorage->getDatabase();

$entry = new UserDocument($oDatabase, $oConfig);
$entry->setProperty('name', 'MadCat');
$entry->setPassword('test');
$entry->setProperty('token', md5("MadCat:Kickipedia2:test"));
$entry->setProperty('email', 'madcat.me@gmail.com');
$entry->save();

$entry = new UserDocument($oDatabase, $oConfig);
$entry->setProperty('name', 'kicki');
$entry->setPassword('pedia');
$entry->setProperty('token', md5("kicki:Kickipedia2:pedia"));
$entry->setProperty('email', 'madcat.me@gmail.com');
$entry->save();