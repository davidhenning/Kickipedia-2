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

class ArrayList extends Base {
 
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
     * Returns all properties as array
     *
     * @return array
     */

    public function getProperties() {
        // get all property names
        $aProperties = array_keys($this->_aProperties);
        $aValues = array();

        if(!empty($aProperties)) {
            foreach($aProperties as $sProperty) {
                $aValues[$sProperty] = $this->getProperty($sProperty);
            }
        }

        return $aValues;
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
        if(!array_key_exists($sKey, $this->_aProperties)) {
            throw new \OutOfBoundsException("Index '{$sKey}' does not exist");
        }

        unset($this->_aProperties[$sKey]);
    }
}