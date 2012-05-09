<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryRecordCollection;

class EntryListView extends View {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_list.twig';
    protected $_sPaginationBaseUrl = '/entry/list';
    protected $_iType = 1;

    public function setType($iType) {
        $this->_iType = $iType;
    }

    protected function _getEntryRecordCollection() {
        $oEntryRecordCollection = new EntryRecordCollection();

        if($this->_iType !== null) {
            $this->_sPaginationAdditionalUrl = "type/{$this->_iType}";
            $oEntryRecordCollection->findByType($this->_iType, $this->_iCurrentPage, $this->_iPerPage);
        } else {
            $oEntryRecordCollection->findByPage($this->_iCurrentPage, $this->_iPerPage);
        }

        return $oEntryRecordCollection;
    }

    protected function _getTypes() {
        $aRawTypes = array('1' => 'Sperrungen', '2' => 'Verwarnungen', '3' => 'abgelaufene Verwarnungen');
        $aTypes = array();

        foreach($aRawTypes as $sId => $sName) {
            $aType = array(
                'id' => $sId,
                'name' => $sName,
                'url' => "{$this->_sPaginationBaseUrl}/type/{$sId}"
            );

            if($this->_iType == $sId) {
                $aType['active'] = true;
            }

            $aTypes[] = $aType;
        }

        return $aTypes;
    }

    public function render() {
        $oEntryRecordCollection = $this->_getEntryRecordCollection();  
        $this->_aTemplateData['entries'] = $oEntryRecordCollection;
        $this->_aTemplateData['types'] = $this->_getTypes();
        $this->_aTemplateData['pagination'] = $this->_getPagination($oEntryRecordCollection->getTotalRecords());

        parent::render();
    }
}