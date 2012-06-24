<?php

namespace Kickipedia2\Views;

use MongoAppKit\View,
    Kickipedia2\Models\EntryDocumentList;

class EntryListView extends BaseView {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_list.twig';
    protected $_sBaseUrl = '/entry/list';
    protected $_oDocuments = null;
    protected $_sSeachTerm = null;
    protected $_sListType = 'list';
    protected $_sCustomSorting = array();
    protected $_sCustomSortOrder = null;
    protected $_aPossibleParams = array(
        array(
            'property' => '_iSkippedDocuments',
            'param' => 'skip'
        ),
        array(
            'property' => '_iDocumentLimit',
            'param' => 'limit'
        ),
        array(
            'property' => '_iDocumentType',
            'param' => 'type'
        ),
        array(
            'property' => '_sSeachTerm',
            'param' => 'term'
        ),
        array(
            'property' => '_sCustomSortField',
            'param' => 'sort'
        ),
        array(
            'property' => '_sCustomSortOrder',
            'param' => 'direction'
        )
    );

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

    public function setCustomSorting($sField, $sOrder) {
        $this->_sCustomSortField = $sField;
        $this->_sCustomSortOrder = $sOrder;
        $this->addAdditionalUrlParameter('sort', $sField);
        $this->addAdditionalUrlParameter('direction', $sOrder);
    }

    public function getDocuments() {
        if($this->_oDocuments === null) {
            $oEntryDocumentList = new EntryDocumentList($this->_oConfig);
            $iDocumentLimit = $this->getDocumentLimit();

            if(!empty($this->_sCustomSortField)) {
                $oEntryDocumentList->setCustomSorting($this->_sCustomSortField, $this->_sCustomSortOrder);
            }

            if($this->_sListType === 'search') {
                $this->addAdditionalUrlParameter('term', $this->_sSeachTerm);
                $oEntryDocumentList->findByTerm($this->_sSeachTerm, $iDocumentLimit, $this->_iSkippedDocuments);

            } elseif($this->_sListType === 'list') {
                if($this->_iDocumentType === null && $this->_sOutputFormat === 'html') {
                    $this->_iDocumentType = 1;
                }

                if($this->_iDocumentType !== null) {
                    $this->addAdditionalUrlParameter('type', $this->_iDocumentType);
                    $oEntryDocumentList->findByType($this->_iDocumentType, $iDocumentLimit, $this->_iSkippedDocuments);
                } else {
                    $oEntryDocumentList->find($iDocumentLimit, $this->_iSkippedDocuments);
                }

            }

            $this->_oDocuments = $oEntryDocumentList;
        }

        return $this->_oDocuments;
    }

    public function render($oApp) {
        $this->_iFoundDocuments = $this->getDocuments()->getFoundDocuments(); 
        $this->_iTotalDocuments = $this->getDocuments()->getTotalDocuments(); 
        $this->_aTemplateData['view'] = $this;

        return parent::render($oApp);
    }

    protected function _renderJSON($oApp) {
        $aOutput = array(
            'status' => 200,
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => 'GET',
                'url' => $oApp['request']->getPathInfo()
            ),
            'response' => array(        
                'total' => $this->_iTotalDocuments,
                'found' => $this->_iFoundDocuments                    
            )
        );

        foreach($this->_aPossibleParams as $aParam) {
            $value = (isset($this->{$aParam['property']})) ? $this->{$aParam['property']} : null;
            
            if(!empty($value)) {
                $aOutput['request']['params'][$aParam['param']] = $value;
            }
        }

        $aOutput['response']['documents'] = array();

        if(count($this->getDocuments()) > 0) {
            foreach($this->getDocuments() as $oDocument) {
                $aOutput['response']['documents'][] = $oDocument->getProperties();
            }
        }

        return $oApp->json($aOutput);
    }

    protected function _renderXML() {
        $oKickipedia = new \SimpleXMLElement('<kickipedia></kickipedia>');
        
        $oKickipedia->addChild('status', 200);
        $oKickipedia->addChild('date', date('Y-m-d H:i:s'));
        
        $oRequest = $oKickipedia->addChild('request');
        $oRequest->addChild('method', 'GET');
        $oRequest->addChild('url', $oApp['request']->getPathInfo());

        foreach($this->_aPossibleParams as $aParam) {
            $value = (isset($this->{$aParam['property']})) ? $this->{$aParam['property']} : null;
            
            if(!empty($value)) {
                $oRequest->addChild($aParam['param'], $value);
            }
        }

        $oResponse = $oKickipedia->addChild('response');
        $oResponse->addChild('total', $this->_iTotalDocuments);
        $oResponse->addChild('found', $this->_iFoundDocuments);

        $oDocuments = $oResponse->addChild('documents');

        if(count($this->getDocuments()) > 0) {
            foreach($this->getDocuments() as $oDocumentData) {
                $oDocument = $oDocuments->addChild('document');
                foreach($oDocumentData as $key => $value) {
                    $oDocument->addChild($key, $oDocumentData->getProperty($key));
                }
            }
        }

        echo $oKickipedia->asXML();
    }
}