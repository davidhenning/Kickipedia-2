<?php

namespace MongoAppKit;

class Storage {

    protected $_oMongo = null;
    protected $_oDatabase = null;

    public function __construct($oConfig) {
        $this->_oMongo = new \Mongo($oConfig->getProperty('MongoServer'));
        $this->_oDatabase = $this->_oMongo->selectDB($oConfig->getProperty('MongoDatabase'));
    }

    public function __destruct() {
        $this->_oMongo->close();
    }

    public function getDatabase() {
        return $this->_oDatabase;
    }
}