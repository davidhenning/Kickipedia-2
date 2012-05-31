<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryDocumentList;

class EntryListView extends BaseView {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_list.twig';
    protected $_sBaseUrl = '/entry/list';
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
            $iCurrentPage = $this->getCurrentPage();

            if($this->_sListType === 'search') {
                $this->addAdditionalUrlParameter('term', $this->_sSeachTerm);
                $this->_sBaseUrl = "/entry/search";
                $oEntryDocumentList->search($this->_sSeachTerm, $iCurrentPage, $this->_iDocumentLimit);

            } elseif($this->_sListType === 'list') {
                
                if($this->_iDocumentType !== null) {
                    $this->addAdditionalUrlParameter('type', $this->_iDocumentType);
                    $oEntryDocumentList->findByType($this->_iDocumentType, $iCurrentPage, $this->_iDocumentLimit);
                } else {
                    $oEntryDocumentList->findByPage($iCurrentPage, $this->_iDocumentLimit);
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
            'status' => 200,
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => 'GET',
                'url' => request_uri(),
                'params' => array(
                    'type' => $this->_iDocumentType,
                    'skip' => $this->_iSkippedDocuments,
                    'limit' => $this->_iDocumentLimit
                )
            ),
            'response' => array(
                'header' => array(              
                      'total' => $this->_iTotalDocuments,
                      'found' => $this->_iFoundDocuments                     
                )
            )
        );

        if($this->_sListType === 'search') {
            $aOutput['request']['params']['term'] = $this->_sSeachTerm;
        }        

        $aOutput['response']['documents'] = array();

        if(count($this->getDocuments()) > 0) {
            foreach($this->getDocuments() as $oDocument) {
                $aOutput['response']['documents'][] = $oDocument->getProperties();
            }
        }

        echo json_encode($aOutput);
    }

    protected function _renderXML() {
        $oKickipedia = new \SimpleXMLElement('<kickipedia></kickipedia>');
        
        $oHeader = $oKickipedia->addChild('header');
        $oHeader->addChild('status', 200);
        $oHeader->addChild('requestMethod', 'GET');
        $oHeader->addChild('date', date('Y-m-d H:i:s'));     

        if($this->_sListType === 'search') {
            $oHeader->addChild('searchTerm', $this->_sSeachTerm);
        }   

        $oDocuments = $oKickipedia->addChild('documents');

        if(count($this->getDocuments()) > 0) {
            foreach($this->getDocuments() as $oDocumentData) {
                $oDocument = $oDocuments->addChild('document');
                $oDocument->addAttribute('id', $oDocumentData->getProperty('_id'));
                foreach($oDocumentData as $key => $value) {
                    if($key !== '_id') {
                        $oDocument->addChild($key, $oDocumentData->getProperty($key));
                    }
                }
            }
        }

        echo $oKickipedia->asXML();
    }
}