<?php

namespace Kickipedia2\Views;

use MongoAppKit\View,
    Kickipedia2\Models\EntryDocument;

class EntryEditView extends BaseView {
    
    protected $_appName = 'Kickipedia2';
    protected $_templateName = 'entry_edit.twig';
    protected $_document = null;
    protected $_showEditTools = true;

    public function render($app) {
        $this->_document = new EntryDocument($app);
        $id = $this->getId();

        if(!empty($id)) {
            $this->_document->load($id);
        }

        $this->_templateData['view'] = $this;

        return parent::render($app);
    }

    public function showEditTools() {
        return $this->_showEditTools;
    }

    public function getDocument() {
        if($this->_document === null) {
            $this->_document = new EntryDocument($this->_app);
            $id = $this->getId();

            if(!is_null($id)) {
                $this->_document->load($id);
            }
        }

        return $this->_document;
    }

    public function getUpdateUrl() {
        return "/entry/{$this->getId()}/update";
    }

    public function getTypeId() {
        return $this->_document->getProperty('type');
    }

    public function renderJsonDeleteResponse($app) {
        $output = array(
            'status' => 202,
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => 'DELETE',
                'url' => $app['request']->getPathInfo()
            ),
            'response' => array(    
                'action' => 'delete',   
                'documentId' => $this->getId()                
            )
        );

        return $app->json($output, 202);
    }

    public function renderJsonUpdateResponse($app) {
        $id = $this->_document->getProperty('_id');

        $httpReponseCode = (!is_null($this->getId())) ? 202 : 201;

        $output = array(
            'status' => $httpReponseCode,
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => $_SERVER['REQUEST_METHOD'],
                'url' => $app['request']->getPathInfo()
            ),
            'response' => array(
                'action' => (!is_null($this->getId())) ? 'update' : 'create',     
                'documentId' => $id,
                'documentUri' => "/entry/{$id}"
            )
        );

        return $app->json($output, $httpReponseCode);
    }
}