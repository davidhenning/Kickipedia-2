<?php

namespace Kickipedia2\Models;

use MongoAppKit\RecordCollection;

class EntryRecordCollection extends RecordCollection {
    protected $_sCollectionName = 'entry';
    protected $_sRecordClass = '\Kickipedia2\Models\EntryRecord';

    public function findByType($iType, $iPage = 1, $iPerPage = 50) {
        $oCursor = $this->_getDefaultCursor(array('type' => $iType));
        $this->findByPage($iPage, $iPerPage, $oCursor);
    }
}