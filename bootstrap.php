<?php

if(!function_exists('getBasePath')) {
    function getBasePath() {
        return dirname(__FILE__).'/';
    }
}

require_once(__DIR__ . '/vendor/autoload.php');
