<?php

namespace MongoAppKit;

use MongoAppKit\Base;

abstract class ArrayList extends Base {
    
    protected $_aProperties = array();

    public function assign(array $aProperties) {
        $this->_aProperties = $aProperties;
    }

    public function getProperty($sKey) {
        if(array_key_exists($sKey, $this->_aProperties)) {
            return $this->_aProperties[$sKey];
        }
        
        throw new \OutOfBoundsException("Index '{$sKey}' does not exist");
    }

    public function setProperty($sKey, $value) {
        $this->_aProperties[$sKey] = $value;
    }

    public function removeProperty($sKey) {
        unset($this->_aProperties[$sKey]);
    }  

}