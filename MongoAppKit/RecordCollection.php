<?php

namespace MongoAppKit;

abstract class RecordCollection extends IterateableList {
    protected $_oDatabase = null;
    protected $_sCollectionName = null;
    protected $_oCollection = null;
    protected $_sRecordClass = null;

    public function __construct() {
        $this->_oDatabase = $this->getStorage()->getDatabase();
        
        if($this->_sCollectionName !== null) {
            $this->_oCollection = $this->_oDatabase->selectCollection($this->_sCollectionName);
        }
    }

    public function findAll() {
        $aData = array();
        $oCursor = $this->_oCollection->find(array(), array('_id' => true))->sort(array('createdOn' => -1));

        if($oCursor === null) {
            throw new \Exception("Document id '{$sId}' does not exist!");
        }

        foreach($oCursor as $oLine) {
            $aData[] = new $this->_sRecordClass($oLine['_id']->{'$id'});
        }

        $this->_aProperties = $aData;
    }
}