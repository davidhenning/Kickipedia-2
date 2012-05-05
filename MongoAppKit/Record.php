<?php

namespace MongoAppKit;

use MongoAppKit\Storage;

abstract class Record extends IterateableList {

    protected $_oDatabase = null;
    protected $_sCollectionName = null;
    protected $_oCollection = null;
    protected $_aPropertyConfig = array();

    public function __construct() {
        $this->_oDatabase = $this->getStorage()->getDatabase();
        
        if($this->_sCollectionName !== null) {
            $this->_oCollection = $this->_oDatabase->selectCollection($this->_sCollectionName);
            $this->_loadRecordConfig();
        }
    }

    protected function _loadRecordConfig() {
        $aPropertyConfig = $this->getConfig()->getProperty('Fields');

        if(isset($aPropertyConfig[$this->_sCollectionName]) && count($aPropertyConfig[$this->_sCollectionName]) > 0) {
            $this->_aPropertyConfig = $aPropertyConfig[$this->_sCollectionName];
        }
    }

    protected function _getPreparedProperties() {
        $aPreparedProperties = array();

        if(!empty($this->_aPropertyConfig)) {
            foreach($this->_aPropertyConfig as $property => $options) {
                $value = (isset($this->_aProperties[$property])) ? $this->_aProperties[$property] : null;
                $aPreparedProperties[$property] = $this->_setPropertyOptions($property, $value, $options);
            }
        }

        return $aPreparedProperties;
    }

    protected function _setPropertyOptions($property, $value, $options) {
         if(!empty($options)) {
            if(isset($options['type'])) {
                if($options['type'] === 'date') {
                    if(!$value instanceof \MongoDate || $property == 'updatedOn') {
                        $value = new \MongoDate();
                    }                 
                }          
            }

            if(isset($options['index']['use']) && $options['index']['use'] === true) {
                $this->_getCollection()->ensureIndex($property);
            }

            if(isset($options['encrypt'])) {
                // for future use       
            }
        }

        return $value; 
    }

    protected function _getCollection() {
        if(!$this->_oCollection instanceof \MongoCollection) {
            throw new \Exception('No collection selected!');
        }

        return $this->_oCollection;
    }

    public function getProperty($key) {
        $value = parent::getProperty($key);

        if($value instanceof \MongoDate) {
            $value = $value->sec;
        }

        return $value;
    }

    public function setProperty($key, $value) {
        if($key === '_id') {
            throw new \Exception('Manual change of id property is prohibited!');
        }
        
        return parent::setProperty($key, $value);
    }

    public function _getCollectionName() {
        return $this->_sCollectionName;
    }

    public function getId() {
        $this->_setId();
        return $this->_aProperties['_id']->{'$id'};
    }

    protected function _setId() {
        if(!$this->_aProperties['_id'] instanceof \MongoId) {
            $this->_aProperties['_id'] = new \MongoId();
        }
    }

    public function updateProperties($aProperties) {
        if(!empty($aProperties)) {
            foreach($aProperties as $property => $value) {
                $this->setProperty($property, $value);
            }
        }
    }

    public function load($id) {
        $aData = $this->_getCollection()->findOne(array('_id' => new \MongoId($id)));
        
        if($aData === null) {
            throw new \Exception("Document id '{$id}' does not exist!");
        }

        $this->_aProperties = $aData;
    }

    public function save() {
        $this->_setId();
        $aPreparedProperties = $this->_getPreparedProperties();
        $this->_getCollection()->save($aPreparedProperties);
    }

    public function remove() {
        $this->_getCollection()->remove(array('_id' => $this->_aProperties['_id']));
    }
}