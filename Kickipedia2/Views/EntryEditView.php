<?php

namespace Kickipedia2\Views;

use MongoAppKit\View,
    Kickipedia2\Models\EntryDocument;

class EntryEditView extends BaseView {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_edit.twig';
    protected $_oDocument = null;
    protected $_bShowEditTools = true;

    public function render() {
        $this->_oDocument = new EntryDocument();
        $sId = $this->getId();

        if(!empty($sId)) {
            $this->_oDocument->load($sId);
        }

        $this->_aTemplateData['view'] = $this;

        parent::render();
    }

    public function showEditTools() {
        return $this->_bShowEditTools;
    }

    public function getDocument() {
        return $this->_oDocument;
    }

    public function getUpdateUrl() {
        return url_for('entry', $this->getId(), 'update');
    }

    public function update($aData) {
        $this->_oDocument = new EntryDocument();
        $sId = $this->getId();

        if(!is_null($sId)) {
            $this->_oDocument->load($sId);
        }
            
        $this->_oDocument->updateProperties($aData);
        $this->_oDocument->save();  
    }

    public function delete() {
        $this->_oDocument = new EntryDocument();
        $this->_oDocument->load($this->getId()); 
        $this->_oDocument->delete();     
    }

    public function getTypeId() {
        return $this->_oDocument->getProperty('type');
    }

    public function renderDeleteResponse() {
        $aOutput = array(
            'header' => array(
                'status' => 200,
                'requestMethod' => 'DELETE',
                'queryType' => 'delete',
                'documentId' => $this->getId()
            )
        );

        echo json_encode($aOutput); 
    }

    public function renderPutResponse() {
        $sId = $this->_oDocument->getProperty('_id');
        $aOutput = array(
            'header' => array(
                'status' => 200,
                'requestMethod' => 'PUT',
                'queryType' => (!is_null($this->getId())) ? 'update' : 'create',
                'documentId' => $this->_oDocument->getProperty('_id'),
                'documentUri' => "/entry/{$sId}"
            )
        );

        echo json_encode($aOutput);
    }
}