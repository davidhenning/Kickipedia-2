<?php

namespace Kickipedia2\Models;

use MongoAppKit\Documents\DocumentList;

class EntryDocumentList extends DocumentList {
    protected $_sCollectionName = 'entry';
    protected $_sDocumentClass = '\Kickipedia2\Models\EntryDocument';

    public function findByType($iType, $iPage = 1, $iPerPage = 50) {
        $oCursor = $this->_getDefaultCursor(array('type' => $iType));
        $this->findByPage($iPage, $iPerPage, $oCursor);
    }
}