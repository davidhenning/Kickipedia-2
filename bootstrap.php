<?php

if(!function_exists('getBasePath')) {
    function getBasePath() {
        return dirname(__FILE__).'/';
    }
}

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/MongoAppKit/Loader.php');
require_once(__DIR__ . '/Kickipedia2/Loader.php');

\MongoAppKit\Loader::registerAutoloader();
\Kickipedia2\Loader::registerAutoloader();