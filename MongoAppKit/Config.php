<?php

namespace MongoAppKit;

class Config extends ArrayList {

    public function __construct() {

        $configFile = getBasePath().'config.json';

        if(!file_exists($configFile)) {
            throw new Exception('Configuration file missing!');
        }

        $configFileJsonData = file_get_contents($configFile);
        $configData = json_decode($configFileJsonData, true);
        $this->assign($configData);
    }

}