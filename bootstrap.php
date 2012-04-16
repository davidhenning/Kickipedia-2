<?php

if(!function_exists('getBasePath')) {
    function getBasePath() {
        return dirname(__FILE__).'/';
    }
}

require_once(__DIR__ . '/lib/limonade.php');
require_once(__DIR__ . '/lib/Twig/Autoloader.php');

spl_autoload_register(function($className) {
    $classPath = __DIR__ . '/' . 
                 implode(DIRECTORY_SEPARATOR, explode('\\', $className)) . '.php';

    if(!file_exists($classPath)) {
        return false;
    }
    
    require_once($classPath);
});

\Twig_Autoloader::register();