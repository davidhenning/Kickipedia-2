<?php

/**
 * Class IterateableList
 *
 * Implements the SPL interfaces Countable, Iterator and ArrayAccess to emulate the full capabilities of an PHP array
 * 
 * @author David Henning <madcat.me@gmail.com>
 * 
 * @package MongoAppKit
 */

namespace MongoAppKit\Lists;

abstract class IterateableList extends ArrayList implements \Countable, \Iterator, \ArrayAccess {

    /**
     * Returns count of object properties
     *
     * @return int
     */

    public function count() {
        return count($this->_aProperties);
    }

    /**
     * Resets internal pointer of properties to first item
     *
     * @return mixed
     */

    public function rewind() {
        return reset($this->_aProperties);
    }

    /**
     * Advances the internal pointer of properties by one and returns the value
     *
     * @return mixed
     */

    public function next() {
        return next($this->_aProperties);
    }

    /**
     * Returns the key (property name) of the current pointer position
     *
     * @return mixed
     */
    
    public function key() {
        return key($this->_aProperties);
    }   
  
    /**
     * Returns the value of the current pointer position
     *
     * @return mixed
     */

    public function current() {
        return current($this->_aProperties);
    }

    /**
     * Returns if the current position is valid
     *
     * @return bool
     */
    
    public function valid() {
        return (bool)$this->current();
    }

    /**
     * Sets a property and its value
     *
     * @param string $sKey
     * @param mixed $value
     */

    public function offsetSet($sKey, $value) {
        $this->setProperty($sKey, $value);
    }

    /**
     * Checks if given property name exists
     *
     * @param string $sKey
     * @return bool
     */
    
    public function offsetExists($sKey) {
        return isset($this->_aProperties[$sKey]);
    }

    /**
     * Removes a property
     *
     * @param string $sKey
     */
    
    public function offsetUnset($sKey) {
        $this->removeProperty($sKey);
    }

    /**
     * Returns value of given property name
     *
     * @param string $sKey
     * @return mixed
     */   

    public function offsetGet($sKey) {
        return $this->getProperty($sKey);
    }
    
}