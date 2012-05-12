<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryDocumentList;

class EntryListView extends View {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_list.twig';
    protected $_sPaginationBaseUrl = '/entry/list';
    protected $_iType = 1;
    protected $_oDocuments = null;
    protected $_aTypes = null;

    public function setType($iType) {
        $this->_iType = $iType;
    }

    public function getDocuments() {
        if($this->_oDocuments === null) {
            $oEntryDocumentList = new EntryDocumentList();

            if($this->_iType !== null) {
                $this->_sPaginationAdditionalUrl = "type/{$this->_iType}";
                $oEntryDocumentList->findByType($this->_iType, $this->_iCurrentPage, $this->_iPerPage);
            } else {
                $oEntryDocumentList->findByPage($this->_iCurrentPage, $this->_iPerPage);
            }

            $this->_oDocuments = $oEntryDocumentList;
        }

        return $this->_oDocuments;
    }

    public function getTypes() {
        if($this->_aTypes === null) {
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

            $this->_aTypes = $aTypes;          
        }

        return $aTypes;
    }

    public function render() {
        $this->_iTotalDocuments = $this->getDocuments()->getTotalDocuments(); 
        $this->_aTemplateData['view'] = $this;

        parent::render();
    }
}