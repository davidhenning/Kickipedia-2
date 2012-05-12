<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryDocumentList;

class EntryListView extends View {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_list.twig';
    protected $_sPaginationBaseUrl = '/entry/list';
    protected $_iType = 1;

    public function setType($iType) {
        $this->_iType = $iType;
    }

    protected function _getEntryDocumentList() {
        $oEntryDocumentList = new EntryDocumentList();

        if($this->_iType !== null) {
            $this->_sPaginationAdditionalUrl = "type/{$this->_iType}";
            $oEntryDocumentList->findByType($this->_iType, $this->_iCurrentPage, $this->_iPerPage);
        } else {
            $oEntryDocumentList->findByPage($this->_iCurrentPage, $this->_iPerPage);
        }

        return $oEntryDocumentList;
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
        $oEntryDocumentList = $this->_getEntryDocumentList();  
        $this->_aTemplateData['entries'] = $oEntryDocumentList;
        $this->_aTemplateData['types'] = $this->_getTypes();
        $this->_aTemplateData['pagination'] = $this->_getPagination($oEntryDocumentList->getTotalDocuments());

        parent::render();
    }
}