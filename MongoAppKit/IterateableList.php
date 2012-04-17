<?php

namespace MongoAppKit;

use MongoAppKit\ArrayList;

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

    public function offsetSet($key, $value) {
        $this->setProperty($key, $value);
    }
    
    public function offsetExists($key) {
        return isset($this->_aProperties[$key]);
    }
    
    public function offsetUnset($key) {
        $this->removeProperty($key);
    }
    
    public function offsetGet($key) {
        return $this->getProperty($key);
    }
    
}