<?php

/**
 * Class Config
 *
 * Reads and stores data from a the config file and provides object access via Singleton pattern
 * 
 * @author David Henning <madcat.me@gmail.com>
 * 
 * @package MongoAppKit
 */

namespace MongoAppKit;

use MongoAppKit\Lists\IterateableList;

class Config extends IterateableList {

    /**
     * Config object
     * @var Config
     */

    private static $_oInstance = null;

    /**
     * Return instance of class Config
     *
     * @return Config
     */

    public static function getInstance() {
        if(self::$_oInstance === null) {
            self::$_oInstance = new Config();
        }

        return self::$_oInstance;
    }

    /**
     * Reads config file and assigns the config data
     */

    private function __construct() {

        $configFile = getBasePath().'mongoappkit.json';

        if(!file_exists($configFile)) {
            throw new Exception('Configuration file missing!');
        }

        $configFileJsonData = file_get_contents($configFile);
        $configData = json_decode($configFileJsonData, true);
        $this->assign($configData);
    }

    public function addConfigFile($sFileName = null) {
        if(empty($sFileName)) {
            throw new \InvalidArgumentException("Empty config file name specified.");
        }

        $configFile = getBasePath().$sFileName;

        if(!is_readable($configFile)) {
            throw new \InvalidArgumentException("File {$sFileName} is not readable!");
        }

        $configFileJsonData = file_get_contents($configFile);
        $configData = json_decode($configFileJsonData, true);
        $this->updateProperties($configData);
    }

    /**
     * Prohibit cloning of the class object (Singleton pattern)
     */

    public function __clone() {
        return null;
    }

}