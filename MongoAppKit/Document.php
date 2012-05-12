<?php

/**
 * Class Document
 *
 * Small implementation of ActiveRecord pattern for MongoDB documents
 * 
 * @author David Henning <madcat.me@gmail.com>
 * 
 * @package MongoAppKit
*/

namespace MongoAppKit;

abstract class Document extends IterateableList {

    /**
     * MongoDB object
     * @var MongoDB
    */

    protected $_oDatabase = null;

    /**
     * Collection name
     * @var string
    */

    protected $_sCollectionName = null;

    /**
     * MongoCollection object
     * @var MongoCollection
    */

    protected $_oCollection = null;

    /**
     * Config data for collection
     * @var array
    */

    protected $_aCollectionConfig = array();

    /**
     * Accesses database, sets current Collection, loads config data for collection and loads document data if an document id is given
     *
     * @param string $sId
    */

    public function __construct($sId = null) {
        $this->_oDatabase = $this->getStorage()->getDatabase();
        
        if($this->_sCollectionName !== null) {
            $this->_oCollection = $this->_oDatabase->selectCollection($this->_sCollectionName);
            $this->_loadCollectionConfig();
        }

        if($sId !== null) {
            $this->load($sId);
        }
    }

    /**
     * Loads collection config from config object and stores it interally
    */

    protected function _loadCollectionConfig() {
        $aPropertyConfig = $this->getConfig()->getProperty('Fields');

        if(isset($aPropertyConfig[$this->_sCollectionName]) && count($aPropertyConfig[$this->_sCollectionName]) > 0) {
            $this->_aCollectionConfig = $aPropertyConfig[$this->_sCollectionName];
        }
    }

    /**
     * Iterates all properties and prepares them for saving in the selected Collection
    */

    protected function _getPreparedProperties() {
        $aPreparedProperties = array();

        if(!empty($this->_aCollectionConfig)) {
            foreach($this->_aCollectionConfig as $sProperty => $aFieldConfig) {
                $value = (isset($this->_aProperties[$sProperty])) ? $this->_aProperties[$sProperty] : null;
                $aPreparedProperties[$sProperty] = $this->_prepareProperty($sProperty, $value, $aFieldConfig);
            }
        }

        return $aPreparedProperties;
    }

    /**
     * Prepares a proptery for saving
     *
     * @param string $sProperty
     * @param mixed $value
     * @param $aFieldConfig
    */

    protected function _prepareProperty($sProperty, $value, $aFieldConfig) {
         if(!empty($aFieldConfig)) {
            if(isset($aFieldConfig['type'])) {
                if($aFieldConfig['type'] === 'date') {
                    if(!$value instanceof \MongoDate || $sProperty == 'updatedOn') {
                        $value = new \MongoDate();
                    }                 
                }          
            }

            if(isset($aFieldConfig['index']['use']) && $aFieldConfig['index']['use'] === true) {
                $this->_getCollection()->ensureIndex($sProperty);
            }

            if(isset($aFieldConfig['encrypt'])) {
                // for future use       
            }
        }

        return $value; 
    }

    /**
     * Returns MongoCollection object or throws an exception if it's not set
     *
     * @throws Exception
     * @return MongoCollection
    */

    protected function _getCollection() {
        if(!$this->_oCollection instanceof \MongoCollection) {
            throw new \Exception('No collection selected!');
        }

        return $this->_oCollection;
    }

    /**
     * Overrides parent method to perpare certain values (f.e. MongoDate) to return their value
     *
     * @param string $sKey
     * @param mixed $value
    */

    public function getProperty($sKey) {
        $value = parent::getProperty($sKey);

        if($value instanceof \MongoDate) {
            $value = $value->sec;
        }

        return $value;
    }

    /**
     * Returns id of current document
     *
     * @return string
    */

    public function getId() {
        $this->_setId();
        return $this->_aProperties['_id']->{'$id'};
    }

    /**
     * Sets id of current document if none exists
    */

    protected function _setId() {
        if(!isset($this->_aProperties['_id']) || !$this->_aProperties['_id'] instanceof \MongoId) {
            $this->_aProperties['_id'] = new \MongoId();
        }
    }

    /**
     * Updates properties with from given array
     *
     * @param array $aProperties
    */

    public function updateProperties($aProperties) {
        if(!empty($aProperties)) {
            foreach($aProperties as $sProperty => $value) {
                $this->setProperty($sProperty, $value);
            }
        }
    }

    /**
     * Loads documend from given id
     *
     * @param string $sId
    */

    public function load($sId) {
        $aData = $this->_getCollection()->findOne(array('_id' => new \MongoId($sId)));
        
        if($aData === null) {
            throw new \Exception("Document id '{$sId}' does not exist!");
        }

        $this->_aProperties = $aData;
    }

    /**
     * Saves document properties into the selected MongoDB collection
    */

    public function save() {
        $this->_setId();
        $aPreparedProperties = $this->_getPreparedProperties();
        $this->_getCollection()->save($aPreparedProperties);
    }

    /**
     * Deletes current document from the selected MongoDB collection
    */

    public function delete() {
        $this->_getCollection()->remove(array('_id' => $this->_aProperties['_id']));
    }
}