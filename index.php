<?php

// modify if your public files are in an other place than default
$mainPath = realpath(__DIR__);

require_once(realpath($mainPath . '/bootstrap.php'));

use Symfony\Component\HttpFoundation\Request;

use MongoAppKit\Config;

use Kickipedia2\Application,
    Kickipedia2\Controllers\EntryActions;

Request::trustProxyData();

$config = new Config();
$config->setBaseDir(realpath($mainPath));
$config->addConfigFile($config->getConfDir() . '/mongoappkit.json');
$config->addConfigFile($config->getConfDir() . '/kickipedia2.json');

$app = new Application($config);
$app->mount('', new EntryActions());
$app->run();