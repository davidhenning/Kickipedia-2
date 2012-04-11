<?php

namespace App;

abstract class IterateableList implements \Countable, \Iterator {
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

    public function count() {
        return count($this->_aProperties);
    }

    public function rewind() {
        return reset($this->_aProperties);
    }

    public function next() {
        return next($this->_aProperties);
    }
    
    public function key() {
        return key($this->_aProperties);
    }   
    
    public function current() {
        return current($this->_aProperties);
    }
    
    public function valid() {
        return (bool)$this->current();
    }       
}