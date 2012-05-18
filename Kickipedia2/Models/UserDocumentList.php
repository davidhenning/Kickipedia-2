<?php

namespace Kickipedia2\Models;

use MongoAppKit\Documents\DocumentList;

class UserDocumentList extends DocumentList {
    protected $_sCollectionName = 'user';
    protected $_sDocumentClass = '\Kickipedia2\Models\UserDocument';

    public function findByName($sName) {
        $oCursor = $this->_getDefaultCursor(array('name' => $sName));
        $this->_setDocumentsFromCursor($oCursor);
    }

    protected function _isValidUser($sName) {
        $this->findByName($sName);

        if($this->_iDocuments === 1) {
            return true;
        }

        return false;
    }

    public function getUser($sName) {
        if($this->_isValidUser($sName) === true) {
            return $this->_aProperties[0];
        }

        throw new \Exception("Could not find user: {$sName}");
    }
}