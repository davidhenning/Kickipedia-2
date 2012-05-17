<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryDocumentList;

class EntryListView extends BaseView {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_list.twig';
    protected $_sPaginationBaseUrl = '/entry/list';
    protected $_oDocuments = null;
    
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

    public function render() {
        $this->_iTotalDocuments = $this->getDocuments()->getTotalDocuments(); 
        $this->_aTemplateData['view'] = $this;

        parent::render();
    }

    protected function _renderJSON() {
        $aOutput = array(
            'header' => array(
                'status' => 200
            )
        );

        $aOutput['navigation'] = $this->getPagination();
        $aOutput['documents'] = array();

        if(count($this->getDocuments()) > 0) {
            foreach($this->getDocuments() as $oDocument) {
                $aOutput['documents'][] = $oDocument->getProperties();
            }
        }

        echo json_encode($aOutput);
    }
}