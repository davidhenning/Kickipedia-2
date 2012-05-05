<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryRecordCollection;

class EntryList extends View {
    
    protected $_sTemplateName = 'entry_list.twig';
    protected $_sId = null;

    public function render() {
        $oEntryRecordCollection = new EntryRecordCollection();
        $oEntryRecordCollection->findAll();  
        $this->_aTemplateData['entries'] = $oEntryRecordCollection;

        parent::render();
    }
}