<?php

namespace MongoAppKit;

abstract class Base {

    private $_oConfig = null;
    private $_oStorage = null;
    private $_oSession = null;

    public function getConfig() {
        if($this->_oConfig === null) {
            $this->_oConfig = Config::getInstance();
        }

        return $this->_oConfig;
    }

    public function getStorage() {
        if($this->_oStorage === null) {
            $this->_oStorage = Storage::getInstance($this->getConfig());
        }
        
        return $this->_oStorage;       
    }

    public function getSession() {
        if($this->_oSession === null) {
            $this->_oSession = new Storage($this->getConfig());
        }

        return $this->_oSession;       
    }
}