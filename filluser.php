<?php

require_once(__DIR__ . '/bootstrap.php');

use MongoAppKit\Config,
	MongoAppKit\Storage;

use Kickipedia2\Models\UserDocument;

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

$entry = new UserDocument($oApp);
$entry->setProperty('name', 'MadCat');
$entry->setPassword('test');
$entry->setProperty('token', md5("MadCat:Kickipedia2:test"));
$entry->setProperty('email', 'madcat.me@gmail.com');
$entry->save();

$entry = new UserDocument($oApp);
$entry->setProperty('name', 'kicki');
$entry->setPassword('pedia');
$entry->setProperty('token', md5("kicki:Kickipedia2:pedia"));
$entry->setProperty('email', 'madcat.me@gmail.com');
$entry->save();