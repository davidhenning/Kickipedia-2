<?php

namespace MongoAppKit;

class Config extends ArrayList {

    private static $_oInstance = null;

    public static function getInstance() {
        if(self::$_oInstance === null) {
            self::$_oInstance = new Config();
        }

        return self::$_oInstance;
    }

    private function __construct() {

        $configFile = getBasePath().'config.json';

        if(!file_exists($configFile)) {
            throw new Exception('Configuration file missing!');
        }

        $configFileJsonData = file_get_contents($configFile);
        $configData = json_decode($configFileJsonData, true);
        $this->assign($configData);
    }

    public function __clone() {
        return null;
    }

}