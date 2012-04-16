<?php

namespace MongoAppKit;

class Storage {

    private static $_oInstance;

    protected $_oMongo = null;
    protected $_oDatabase = null;

    public static function getInstance() {
        if(!self::$_oInstance instanceof self) {
            self::$_oInstance = new self();
        }

        return self::$_oInstance;
    }

    private function __construct() {
        $this->_oMongo = new \Mongo('localhost');
        $this->_oDatabase = $this->_oMongo->selectDB('kickipedia2');
    }

    public function __destruct() {
        $this->_oMongo->close();
    }

    public function __clone() {
        return null;
    }

    public function getDatabase() {
        return $this->_oDatabase;
    }
}