<?php

namespace MongoAppKit;

use MongoAppKit\Base;

abstract class ArrayList extends Base {
    
    protected $_aProperties = array();

    public function assign(array $aProperties) {
        $this->_aProperties = $aProperties;
    }

    public function getProperty($key) {
        if(array_key_exists($key, $this->_aProperties)) {
                return $this->_aProperties[$key];
        }
        
        throw new \OutOfBoundsException("Index '{$key}' does not exist");
    }

    public function setProperty($key, $value) {
        $this->_aProperties[$key] = $value;
    }

    public function removeProperty($key) {
        unset($this->_aProperties[$key]);
    }  

}