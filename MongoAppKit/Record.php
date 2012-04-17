<?php

namespace MongoAppKit;

use MongoAppKit\Storage;

abstract class Record extends IterateableList {

    protected $_oDatabase = null;
    protected $_sCollectionName = null;
    protected $_oCollection = null;

    public function __construct() {
        $this->_oDatabase = $this->getStorage()->getDatabase();
        
        if($this->_sCollectionName !== null) {
            $this->_oCollection = $this->_oDatabase->selectCollection($this->_sCollectionName);
        }
    }

    protected function _getCollection() {
        if(!$this->_oCollection instanceof \MongoCollection) {
            throw new \Exception('No collection selected!');
        }

        return $this->_oCollection;
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

    public function load($id) {
        $aData = $this->_getCollection()->findOne(array('_id' => new \MongoId($id)));
        
        if($aData === null) {
            throw new \Exception("Document id '{$id}' does not exist!");
        }

        $this->_aProperties = $aData;
    }

    public function save() {
        $this->_setId();
        $this->_getCollection()->save($this->_aProperties);
    }

    public function remove() {
        $this->_getCollection()->remove(array('_id' => $this->_aProperties['_id']));
    }
}