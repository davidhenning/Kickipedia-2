<?php

namespace Kickipedia2\Controllers;

use MongoAppKit\Base;

use Kickipedia2\Views\EntryListView,
    Kickipedia2\Views\EntryView,
    Kickipedia2\Views\EntryEditView,
    Kickipedia2\Views\EntryNewView;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

class EntryActions extends Base {

    protected $_oApp = null;

    public function __construct($oApp) {
        $this->_oApp = $oApp;
        $actions = $this;
        
        /* GET actions */

        $oApp->get('/entry/list.{format}', function(Request $oRequest, $format) use($oApp, $actions) {
            return $actions->showList($oRequest, $format);
        })->bind('list_get');

        $oApp->get('/entry/{id}.{format}', function(Request $oRequest, $id, $format) use($oApp, $actions) {
            return $actions->showEntry($oRequest, $id, $format);
        })->bind('view_get');

        $oApp->get('/entry/new', function(Request $oRequest) use ($oApp, $actions) {
            return $actions->newEntry($oRequest);
        })->bind('new_get');

        $oApp->get('/entry/{id}/edit', function(Request $oRequest, $id) use ($oApp, $actions) {
            return $actions->editEntry($oRequest, $id);
        })->bind('edit_get');

        /* PUT actions */

        $oApp->put('/entry', function(Request $oRequest) use ($oApp, $actions) {
            return $actions->updateEntry($oRequest, null);
        })->bind('insert_put');

        $oApp->put('/entry/{id}', function(Request $oRequest, $id) use ($oApp, $actions) {
            return $actions->updateEntry($oRequest, $id);
        })->bind('update_put');

        /* DELETE actions */

        $oApp->delete('/entry/{id}', function(Request $oRequest, $id) use ($oApp, $actions) {
            return $actions->deleteEntry($oRequest, $id);
        })->bind('delete_delete');

        /* POST actions */

        $oApp->post('/entry/insert', function(Request $oRequest) use ($oApp, $actions) {
            return $actions->updateEntry($oRequest);
        })->bind('insert_post');        

        $oApp->post('/entry/{id}/update', function(Request $oRequest, $id) use ($oApp, $actions) {
            return $actions->updateEntry($oRequest, $id);
        })->bind('update_post');

        $oApp->post('/entry/{id}/delete', function(Request $oRequest, $id) use ($oApp, $actions) {
            return $actions->deleteEntry($oRequest, $id);
        })->bind('delete_post');
    }

    public function showList(Request $oRequest, $sFormat) {
        $oConfig = $this->getConfig();

        $iSkip = (int) $oConfig->sanitize($oRequest->query->get('skip'));
        $iLimit = (int) $oConfig->sanitize($oRequest->query->get('limit'));
        $iType = (int) $oConfig->sanitize($oRequest->query->get('type'));
        $sTerm = $oConfig->sanitize($oRequest->query->get('term'));
        $sSort = $oConfig->sanitize($oRequest->query->get('sort'));
        $sDirection = $oConfig->sanitize($oRequest->query->get('direction'));
        $sFormat = $oConfig->sanitize($sFormat);

        $oView = new EntryListView();
        
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
        $oConfig = $this->getConfig();
        $sId = $oConfig->sanitize($sId);
        $sFormat = $oConfig->sanitize($sFormat);
        
        $oView = new EntryView($sId);

        if(!empty($sFormat)) {
            $oView->setOutputFormat($sFormat);
        }

        return $oView->render($this->_oApp);
    }

    public function newEntry(Request $oRequest) {
        $oView = new EntryNewView();
        
        return $oView->render($this->_oApp); 
    }

    public function editEntry(Request $oRequest, $sId) {
        $oConfig = $this->getConfig();
        $sId = $oConfig->sanitize($sId);

        $oView = new EntryEditView($sId);
        return $oView->render($this->_oApp);        
    }

    public function updateEntry(Request $oRequest, $sId) {
        $oConfig = $this->getConfig();
        $sId = $oConfig->sanitize($sId);       
        $aEntryData = $oConfig->sanitize($oRequest->request->get('data'));
        
        $oView = new EntryEditView($sId);
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
        $oConfig = $this->getConfig();
        $sId =  $oConfig->sanitize($sId);
        
        $oView = new EntryEditView($sId);
        $oView->getDocument()->delete();

        if($oRequest->headers->get('content_type') === 'application/json' || $oRequest->getMethod() === 'DELETE') {
            return $oView->renderJsonDeleteResponse($this->_oApp);
        } else {            
            return $oView->redirect($this->_oApp, '/entry/list.html', array('type' => $iTypeId));
        }
    }
}