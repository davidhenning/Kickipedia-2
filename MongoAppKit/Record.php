<?php

namespace MongoAppKit;

abstract class Record extends IterateableList {

    protected $_oDatabase = null;
    protected $_sCollectionName = null;
    protected $_oCollection = null;
    protected $_aRecordConfig = array();

    public function __construct($sId = null) {
        $this->_oDatabase = $this->getStorage()->getDatabase();
        
        if($this->_sCollectionName !== null) {
            $this->_oCollection = $this->_oDatabase->selectCollection($this->_sCollectionName);
            $this->_loadRecordConfig();
        }

        if($sId !== null) {
            $this->load($sId);
        }
    }

    protected function _loadRecordConfig() {
        $aPropertyConfig = $this->getConfig()->getProperty('Fields');

        if(isset($aPropertyConfig[$this->_sCollectionName]) && count($aPropertyConfig[$this->_sCollectionName]) > 0) {
            $this->_aRecordConfig = $aPropertyConfig[$this->_sCollectionName];
        }
    }

    protected function _getPreparedProperties() {
        $aPreparedProperties = array();

        if(!empty($this->_aRecordConfig)) {
            foreach($this->_aRecordConfig as $sProperty => $aOptions) {
                $value = (isset($this->_aProperties[$sProperty])) ? $this->_aProperties[$sProperty] : null;
                $aPreparedProperties[$sProperty] = $this->_setPropertyOptions($sProperty, $value, $aOptions);
            }
        }

        return $aPreparedProperties;
    }

    protected function _setPropertyOptions($sProperty, $value, $aOptions) {
         if(!empty($aOptions)) {
            if(isset($aOptions['type'])) {
                if($aOptions['type'] === 'date') {
                    if(!$value instanceof \MongoDate || $sProperty == 'updatedOn') {
                        $value = new \MongoDate();
                    }                 
                }          
            }

            if(isset($aOptions['index']['use']) && $aOptions['index']['use'] === true) {
                $this->_getCollection()->ensureIndex($sProperty);
            }

            if(isset($aOptions['encrypt'])) {
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

    public function getProperty($sKey) {
        $value = parent::getProperty($sKey);

        if($value instanceof \MongoDate) {
            $value = $value->sec;
        }

        return $value;
    }

    public function _getCollectionName() {
        return $this->_sCollectionName;
    }

    public function getId() {
        $this->_setId();
        return $this->_aProperties['_id']->{'$id'};
    }

    protected function _setId() {
        if(!isset($this->_aProperties['_id']) || !$this->_aProperties['_id'] instanceof \MongoId) {
            $this->_aProperties['_id'] = new \MongoId();
        }
    }

    public function updateProperties($aProperties) {
        if(!empty($aProperties)) {
            foreach($aProperties as $sProperty => $value) {
                $this->setProperty($sProperty, $value);
            }
        }
    }

    public function load($sId) {
        $aData = $this->_getCollection()->findOne(array('_id' => new \MongoId($sId)));
        
        if($aData === null) {
            throw new \Exception("Document id '{$sId}' does not exist!");
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