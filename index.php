<?php

// modify if your public files are in an other place than default
$sMainPath = realpath(__DIR__);

require_once(realpath($sMainPath . '/bootstrap.php'));

use Symfony\Component\HttpFoundation\Request;

use MongoAppKit\Config,
    MongoAppKit\Storage;

use Kickipedia2\Application,
    Kickipedia2\Controllers\EntryActions;

Request::trustProxyData();

$oConfig = new Config();
$oConfig->setBaseDir(realpath($sMainPath));
$oConfig->addConfigFile($oConfig->getConfDir() . '/mongoappkit.json');
$oConfig->addConfigFile($oConfig->getConfDir() . '/kickipedia2.json');
$oStorage = new Storage($oConfig);

$oApp = new Application($oConfig, $oStorage);
$oApp->mount('', new EntryActions());
$oApp->run();