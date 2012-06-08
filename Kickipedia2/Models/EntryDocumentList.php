<?php

namespace Kickipedia2\Models;

use MongoAppKit\Documents\DocumentList;

class EntryDocumentList extends DocumentList {
    protected $_sCollectionName = 'entry';

    public function __construct() {
        parent::__construct();
        $this->setDocumentBaseObject(new EntryDocument());
    }

    public function findByType($iType, $iLimit = 100, $iSkip = 0) {
        $oCursor = $this->_getDefaultCursor(array('type' => $iType));
        $this->find($iLimit, $iSkip, $oCursor);
    }

    public function findByTerm($sTerm, $iLimit = 100, $iSkip = 0) {
        $aWhere = array('$or' => array(
            array('name' => new \MongoRegex("/{$sTerm}/i")),
            array('reason' => new \MongoRegex("/{$sTerm}/i")),
            array('comment' => new \MongoRegex("/{$sTerm}/i"))
        ));

        $oCursor = $this->_getDefaultCursor($aWhere);
        $this->find($iLimit, $iSkip, $oCursor);
    }
}