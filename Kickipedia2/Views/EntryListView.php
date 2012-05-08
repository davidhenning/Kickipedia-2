<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryRecordCollection;

class EntryListView extends View {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_list.twig';
    protected $_sPaginationBaseUrl = '/entry/list';
    protected $_iType = null;

    public function setType($iType) {
        $this->_iType = $iType;
    }

    protected function _getEntryRecordCollection() {
        $oEntryRecordCollection = new EntryRecordCollection();

        if($this->_iType !== null) {
            $this->_sPaginationAdditionalUrl = "type/{$this->_iType}";
            $oEntryRecordCollection->findByType($this->_iType, $this->_iPage, $this->_iPerPage);
        } else {
            $oEntryRecordCollection->findByPage($this->_iPage, $this->_iPerPage);
        }

        return $oEntryRecordCollection;
    }

    public function render() {
        $oEntryRecordCollection = $this->_getEntryRecordCollection();
        $this->_aTemplateData['entries'] = $oEntryRecordCollection;
        $this->_aTemplateData['pagination'] = $this->_getPagination($oEntryRecordCollection->getTotalRecords());

        parent::render();
    }
}