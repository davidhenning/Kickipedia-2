<?php

if(!function_exists('getBasePath')) {
    function getBasePath() {
        return dirname(__FILE__).'/';
    }
}

require_once(__DIR__ . '/MongoAppKit/Loader.php');
require_once(__DIR__ . '/Kickipedia2/Loader.php');
require_once(__DIR__ . '/Kickipedia2/vendor/Twig/Autoloader.php');
require_once(__DIR__ . '/Kickipedia2/vendor/Phpass/Loader.php');

\MongoAppKit\Loader::registerAutoloader();
\Kickipedia2\Loader::registerAutoloader();
\Twig_Autoloader::register();
\Phpass\Loader::registerAutoloader();

require_once(__DIR__ . '/Kickipedia2/vendor/Limonade/limonade.php');
require_once(__DIR__ . '/MongoAppKit/vendor/GibberishAES/GibberishAES.php');