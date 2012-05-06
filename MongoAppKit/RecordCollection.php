<?php

namespace MongoAppKit;

abstract class RecordCollection extends IterateableList {
    protected $_oDatabase = null;
    protected $_sCollectionName = null;
    protected $_oCollection = null;
    protected $_sRecordClass = null;
    protected $_iRecords = 0;
    protected $_iTotalRecords = 0;
    protected $_sCustomSortField = null;
    protected $_sCustomSortOrder = null;

    public function __construct() {
        $this->_oDatabase = $this->getStorage()->getDatabase();
        
        if($this->_sCollectionName !== null) {
            $this->_oCollection = $this->_oDatabase->selectCollection($this->_sCollectionName);
        }
    }

    public function findAll() {       
        $oCursor = $this->_getDefaultCursor();
        $this->_setPropertiesFromCursor($oCursor);
    }

    public function findByPage($iPage = 1, $iPerPage = 50) {
        $oCursor = $this->_getDefaultCursor();
        $oCursor->limit($iPerPage);

        $iSkip = ($iPage - 1) * $iPerPage;

        if($iSkip > 0) {
            $oCursor->skip($iSkip);
        }

        $this->_setPropertiesFromCursor($oCursor);       
    }

    public function getTotalRecords() {
        return $this->_iTotalRecords;
    }

    protected function _getDefaultCursor($aWhere = null, $aFields = null) {
        if($aWhere === null) {
            $aWhere = array();
        }

        if($aFields === null) {
            $aFields = array();
        }

        $oCursor = $this->_oCollection->find($aWhere, $aFields);
        $aSorting = array();

        if($this->_sCustomSortField !== null) {
            $iSortOrder = 1;

            if($this->_sCustomSortField !== null) {
                if($this->_sCustomSortField === 'asc') {
                    $iSortOrder = 1;
                } elseif($this->_sCustomSortField === 'asc') {
                    $iSortOrder = -1;
                } else {
                    $iSortOrder = 1;
                }
            }   

            $aSorting = array($this->_sCustomSortField => $iSortOrder);
        } else {
            $aSorting = array('createdOn' => -1);
        }

        $oCursor->sort($aSorting);

        return $oCursor;     
    }

    protected function _setPropertiesFromCursor(\MongoCursor $oCursor) {
        $aData = array();
        
        if($oCursor === null) {
            throw new \Exception("Document id '{$sId}' does not exist!");
        }

        foreach($oCursor as $oLine) {
            $oRecord = new $this->_sRecordClass();
            $oRecord->updateProperties($oLine);
            $aData[] = $oRecord;
        }

        $this->_iRecords = $oCursor->count(true);
        $this->_iTotalRecords = $oCursor->count();
        $this->_aProperties = $aData;
    }
}