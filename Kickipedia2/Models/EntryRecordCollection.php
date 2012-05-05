<?php

namespace Kickipedia2\Models;

use MongoAppKit\RecordCollection;

class EntryRecordCollection extends RecordCollection {
    protected $_sCollectionName = 'entry';
    protected $_sRecordClass = '\Kickipedia2\Models\EntryRecord';    
}