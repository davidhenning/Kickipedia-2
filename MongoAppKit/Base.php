<?php

/**
 * Class Base
 *
 * Base provides core methods for all classes of MongoAppKit
 * 
 * @author David Henning <madcat.me@gmail.com>
 * 
 * @package MongoAppKit
 */

namespace MongoAppKit;

class Base {

    /**
     * Config object
     * @var Config
     */

    protected $_oConfig = null;

    /**
     * Storage object
     * @var Storage
     */

    protected $_oStorage = null;

    /**
     * Returns Config object
     *
     * @return Config
     */

    public function getConfig() {
        if($this->_oConfig === null) {
            $this->_oConfig = Config::getInstance();
        }

        return $this->_oConfig;
    }

    /**
     * Returns Storage object
     *
     * @return Storage
     */

    public function getStorage() {
        if($this->_oStorage === null) {
            $this->_oStorage = Storage::getInstance($this->getConfig());
        }
        
        return $this->_oStorage;       
    }
}