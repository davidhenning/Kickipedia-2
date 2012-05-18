<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryDocumentList;

class EntryListView extends BaseView {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_list.twig';
    protected $_sPaginationBaseUrl = '/entry/list';
    protected $_oDocuments = null;
    protected $_sSeachTerm = null;
    protected $_sListType = 'list';
    
    public function setDocumentType($iType) {
        $this->_iDocumentType = $iType;
    }

    public function setListType($sType) {
        $this->_sListType = $sType;
    }

    public function getSearchTerm() {
        return $this->_sSeachTerm;
    }

    public function setSearchTerm($sTerm) {
        $this->_sTemplateName = 'entry_search.twig';
        $this->_sSeachTerm = $sTerm;
    }

    public function getTotalDocuments() {
        return $this->_iTotalDocuments;
    }

    public function getFoundDocuments() {
        return $this->_iFoundDocuments;
    }

    public function getDocuments() {
        if($this->_oDocuments === null) {
            $oEntryDocumentList = new EntryDocumentList();

            if($this->_sListType === 'search') {
                $this->addAdditionalUrlParameter('term', $this->_sSeachTerm);
                $this->_sPaginationBaseUrl = "/entry/search";
                $oEntryDocumentList->search($this->_sSeachTerm, $this->_iCurrentPage, $this->_iPerPage);

            } elseif($this->_sListType === 'list') {
                
                if($this->_iDocumentType !== null) {
                    $this->_sPaginationAdditionalUrl = "type/{$this->_iDocumentType}";
                    $oEntryDocumentList->findByType($this->_iDocumentType, $this->_iCurrentPage, $this->_iPerPage);
                } else {
                    $oEntryDocumentList->findByPage($this->_iCurrentPage, $this->_iPerPage);
                }

            }

            $this->_oDocuments = $oEntryDocumentList;
        }

        return $this->_oDocuments;
    }

    public function render() {
        $this->_iFoundDocuments = $this->getDocuments()->getFoundDocuments(); 
        $this->_iTotalDocuments = $this->getDocuments()->getTotalDocuments(); 
        $this->_aTemplateData['view'] = $this;

        parent::render();
    }

    protected function _renderJSON() {
        $aOutput = array(
            'header' => array(
                'status' => 200,
                'requestMethod' => 'GET',
                'queryType' => 'read'
                
            )
        );

        if($this->_sListType === 'list') {
            $aOutput['header']['documentType'] = $this->_iDocumentType;
        }

        if($this->_sListType === 'search') {
            $aOutput['header']['searchTerm'] = $this->_sSeachTerm;
        }        

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