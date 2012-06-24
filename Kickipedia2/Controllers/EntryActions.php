<?php

namespace Kickipedia2\Controllers;

use Kickipedia2\Views\EntryListView,
    Kickipedia2\Views\EntryView,
    Kickipedia2\Views\EntryEditView,
    Kickipedia2\Views\EntryNewView;

use Symfony\Component\HttpFoundation\Request;

class EntryActions extends BaseActions {

    protected function _initRoutes() {
        $oActions = $this;
        
        /* GET actions */

        $this->_oApp->get('/entry/list.{format}', function(Request $oRequest, $format) use($oActions) {
            return $oActions->showList($oRequest, $format);
        })->bind('list_get');

        $this->_oApp->get('/entry/{id}.{format}', function(Request $oRequest, $id, $format) use($oActions) {
            return $oActions->showEntry($oRequest, $id, $format);
        })->bind('view_get');

        $this->_oApp->get('/entry/new', function(Request $oRequest) use ($oActions) {
            return $oActions->newEntry($oRequest);
        })->bind('new_get');

        $this->_oApp->get('/entry/{id}/edit', function(Request $oRequest, $id) use ($oActions) {
            return $oActions->editEntry($oRequest, $id);
        })->bind('edit_get');

        /* PUT actions */

        $this->_oApp->put('/entry', function(Request $oRequest) use ($oActions) {
            return $oActions->updateEntry($oRequest, null);
        })->bind('insert_put');

        $this->_oApp->put('/entry/{id}', function(Request $oRequest, $id) use ($oActions) {
            return $oActions->updateEntry($oRequest, $id);
        })->bind('update_put');

        /* DELETE actions */

        $this->_oApp->delete('/entry/{id}', function(Request $oRequest, $id) use ($oActions) {
            return $oActions->deleteEntry($oRequest, $id);
        })->bind('delete_delete');

        /* POST actions */

        $this->_oApp->post('/entry/insert', function(Request $oRequest) use ($oActions) {
            return $oActions->updateEntry($oRequest);
        })->bind('insert_post');        

        $this->_oApp->post('/entry/{id}/update', function(Request $oRequest, $id) use ($oActions) {
            return $oActions->updateEntry($oRequest, $id);
        })->bind('update_post');

        $this->_oApp->post('/entry/{id}/delete', function(Request $oRequest, $id) use ($oActions) {
            return $oActions->deleteEntry($oRequest, $id);
        })->bind('delete_post');
    }

    public function showList(Request $oRequest, $sFormat) {
        $iSkip = (int) $this->_oConfig->sanitize($oRequest->query->get('skip'));
        $iLimit = (int) $this->_oConfig->sanitize($oRequest->query->get('limit'));
        $iType = (int) $this->_oConfig->sanitize($oRequest->query->get('type'));
        $sTerm = $this->_oConfig->sanitize($oRequest->query->get('term'));
        $sSort = $this->_oConfig->sanitize($oRequest->query->get('sort'));
        $sDirection = $this->_oConfig->sanitize($oRequest->query->get('direction'));
        $sFormat = $this->_oConfig->sanitize($sFormat);

        $oView = new EntryListView($this->_oConfig, $oRequest);
        
        if(!empty($sTerm)) {
            $oView->setListType('search');
            $oView->setSearchTerm($sTerm);
        }

        if($iSkip > 0) {
            $oView->setSkippedDocuments($iSkip);
        }

        if($iLimit > 0) {
            $oView->setDocumentLimit($iLimit);
        }

        if($iType > 0) {
            $oView->setDocumentType($iType);
        }

        if(!empty($sSort)) {
            $sDirection = (!empty($sDirection)) ? $sDirection : null;
            $oView->setCustomSorting($sSort, $sDirection);
        }

        if(!empty($sFormat)) {
            $oView->setOutputFormat($sFormat);
        }

        return $oView->render($this->_oApp);    
    }

    public function showEntry(Request $oRequest, $sId, $sFormat) {
        $sId = $this->_oConfig->sanitize($sId);
        $sFormat = $this->_oConfig->sanitize($sFormat);
        
        $oView = new EntryView($this->_oConfig, $oRequest, $sId);

        if(!empty($sFormat)) {
            $oView->setOutputFormat($sFormat);
        }

        return $oView->render($this->_oApp);
    }

    public function newEntry(Request $oRequest) {
        $oView = new EntryNewView($this->_oConfig, $oRequest);
        
        return $oView->render($this->_oApp); 
    }

    public function editEntry(Request $oRequest, $sId) {
        $sId = $this->_oConfig->sanitize($sId);

        $oView = new EntryEditView($this->_oConfig, $oRequest, $sId);
        return $oView->render($this->_oApp);        
    }

    public function updateEntry(Request $oRequest, $sId) {
        $sId = $this->_oConfig->sanitize($sId);       
        $aEntryData = $this->_oConfig->sanitize($oRequest->request->get('data'));
        
        $oView = new EntryEditView($this->_oConfig, $oRequest, $sId);
        $oDocument = $oView->getDocument();
        $oDocument->updateProperties($aEntryData);
        $oDocument->save();

        try {
            $iTypeId = $oView->getTypeId();
        } catch(\Exception $e) {
            $iTypeId = null;
        }

        if($oRequest->headers->get('content_type') === 'application/json' || $oRequest->getMethod() === 'PUT') {
            return $oView->renderJsonUpdateResponse($this->_oApp);
        } else {
            return $oView->redirect($this->_oApp, '/entry/list.html', array('type' => $iTypeId));
        }          
    }

    public function deleteEntry(Request $oRequest, $sId) {
        $sId =  $this->_oConfig->sanitize($sId);
        
        $oView = new EntryEditView($this->_oConfig, $oRequest, $sId);
        $oView->getDocument()->delete();

        if($oRequest->headers->get('content_type') === 'application/json' || $oRequest->getMethod() === 'DELETE') {
            return $oView->renderJsonDeleteResponse($this->_oApp);
        } else {            
            return $oView->redirect($this->_oApp, '/entry/list.html', array('type' => $iTypeId));
        }
    }
}