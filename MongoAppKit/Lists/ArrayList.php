<?php

/**
 * Class ArrayList
 *
 * Stores properties in an array and provide methods to access, set and delete them
 * 
 * @author David Henning <madcat.me@gmail.com>
 * 
 * @package MongoAppKit
 */

namespace MongoAppKit\Lists;

use MongoAppKit\Base;

abstract class ArrayList extends Base {
 
    /**
     * Stores properties
     * @var array
     */

    protected $_aProperties = array();

    /**
     * Imports an array 
     *
     * @param array $aProperties
     */

    public function assign(array $aProperties) {
        $this->_aProperties = $aProperties;
    }

    /**
     * Returns value of given property name or throws an exception if the property does not exist 
     *
     * @param string $sKey
     * @return mixed
     * @throws OutOfBoundsException
     */

    public function getProperty($sKey) {
        if(array_key_exists($sKey, $this->_aProperties)) {
            return $this->_aProperties[$sKey];
        }
        
        throw new \OutOfBoundsException("Index '{$sKey}' does not exist");
    }

    /**
     * Sets a property and its value
     *
     * @param string $sKey
     * @param mixed $value
     */

    public function setProperty($sKey, $value) {
        $this->_aProperties[$sKey] = $value;
    }

    /**
     * Removes a property
     *
     * @param string $sKey
     */

    public function removeProperty($sKey) {
        unset($this->_aProperties[$sKey]);
    }  

}