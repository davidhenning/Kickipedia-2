<?php

namespace Kickipedia2\Models;

use MongoAppKit\Config,
    MongoAppKit\Documents\DocumentList;

class UserDocumentList extends DocumentList {
    protected $_sCollectionName = 'user';

    public function __construct(Config $oConfig) {
        parent::__construct($oConfig);
        $this->setDocumentBaseObject(new UserDocument($this->_oDatabase, $oConfig));
    }

    public function findByName($sName) {
        $oCursor = $this->_getDefaultCursor(array('name' => $sName));
        $this->_setDocumentsFromCursor($oCursor);
    }

    protected function _isValidUser($sName) {
        $this->findByName($sName);

        if($this->_iFoundDocuments === 1) {
            return true;
        }

        return false;
    }

    public function getUser($sName) {
        if($this->_isValidUser($sName) === true) {
            return $this->_aProperties[0];
        }

        throw new \InvalidArgumentException("Could not find user: {$sName}");
    }
}