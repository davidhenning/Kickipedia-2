<?php

namespace MongoAppKit;

abstract class IterateableList extends ArrayList implements \Countable, \Iterator, \ArrayAccess {

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

    public function offsetSet($sKey, $value) {
        $this->setProperty($sKey, $value);
    }
    
    public function offsetExists($sKey) {
        return isset($this->_aProperties[$sKey]);
    }
    
    public function offsetUnset($sKey) {
        $this->removeProperty($sKey);
    }
    
    public function offsetGet($sKey) {
        return $this->getProperty($sKey);
    }
    
}