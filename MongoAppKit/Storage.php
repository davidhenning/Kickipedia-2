<?php

namespace MongoAppKit;

class Storage {

    private static $_oInstance = null;

    protected $_oMongo = null;
    protected $_oDatabase = null;

    public static function getInstance($oConfig) {
        if(self::$_oInstance === null) {
            self::$_oInstance = new Storage($oConfig);
        }

        return self::$_oInstance;
    }

    private function __construct($oConfig) {
        $this->_oMongo = new \Mongo($oConfig->getProperty('MongoServer'));
        $this->_oDatabase = $this->_oMongo->selectDB($oConfig->getProperty('MongoDatabase'));
    }

    public function __clone() {
        return null;
    }

    public function __destruct() {
        $this->_oMongo->close();
    }

    public function getDatabase() {
        return $this->_oDatabase;
    }
}