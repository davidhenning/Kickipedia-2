<?php

namespace Kickipedia2\Views;

use MongoAppKit\View,
    Kickipedia2\Models\EntryDocument;

class EntryEditView extends BaseView {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_edit.twig';
    protected $_oDocument = null;
    protected $_bShowEditTools = true;

    public function render($oApp) {
        $this->_oDocument = new EntryDocument($this->_oConfig->getProperty('storage')->getDatabase(), $this->_oConfig);
        $sId = $this->getId();

        if(!empty($sId)) {
            $this->_oDocument->load($sId);
        }

        $this->_aTemplateData['view'] = $this;

        return parent::render($oApp);
    }

    public function showEditTools() {
        return $this->_bShowEditTools;
    }

    public function getDocument() {
        if($this->_oDocument === null) {
            $this->_oDocument = new EntryDocument($this->_oConfig->getProperty('storage')->getDatabase(), $this->_oConfig);
            $sId = $this->getId();

            if(!is_null($sId)) {
                $this->_oDocument->load($sId);
            }
        }

        return $this->_oDocument;
    }

    public function getUpdateUrl() {
        return "/entry/{$this->getId()}/update";
    }

    public function getTypeId() {
        return $this->_oDocument->getProperty('type');
    }

    public function renderJsonDeleteResponse($oApp) {
        $aOutput = array(
            'status' => 202,
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => 'DELETE',
                'url' => $oApp['request']->getPathInfo()
            ),
            'response' => array(    
                'action' => 'delete',   
                'documentId' => $this->getId()                
            )
        );

        return $oApp->json($aOutput, 202);
    }

    public function renderJsonUpdateResponse($oApp) {
        $sId = $this->_oDocument->getProperty('_id');

        $iHttpReponseCode = (!is_null($this->getId())) ? 202 : 201;

        $aOutput = array(
            'status' => $iHttpReponseCode,
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => $_SERVER['REQUEST_METHOD'],
                'url' => $oApp['request']->getPathInfo()
            ),
            'response' => array(
                'action' => (!is_null($this->getId())) ? 'update' : 'create',     
                'documentId' => $sId,
                'documentUri' => "/entry/{$sId}"              
            )
        );

        return $oApp->json($aOutput, $iHttpReponseCode);
    }
}