<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryRecordCollection;

class EntryList extends View {
    
    protected $_sTemplateName = 'entry_list.twig';
    protected $_sId = null;
    protected $_sPaginationBaseUrl = '/entry/list';

    public function render() {
        $oEntryRecordCollection = new EntryRecordCollection();
        $oEntryRecordCollection->findByPage($this->_iPage, $this->_iPerPage);  
        $this->_aTemplateData['entries'] = $oEntryRecordCollection;
        $this->_aTemplateData['pages'] = $this->_getPagination($oEntryRecordCollection->getTotalRecords());

        parent::render();
    }
}