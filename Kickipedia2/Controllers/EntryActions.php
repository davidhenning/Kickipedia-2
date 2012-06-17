<?php

namespace Kickipedia2\Controllers;

use MongoAppKit\Input,
    Kickipedia2\Views\EntryListView,
    Kickipedia2\Views\EntryView,
    Kickipedia2\Views\EntryEditView,
    Kickipedia2\Views\EntryNewView;

class EntryActions {

    public function __construct() {
        // PUT actions for REST service
        dispatch_put('/entry', array($this, 'updateEntry'));
        dispatch_put('/entry/:id', array($this, 'updateEntry'));
        
        // DELETE actions for REST service
        dispatch_delete('/entry/:id', array($this, 'deleteEntry'));

        // POST actions
        dispatch_post('/entry/insert', array($this, 'updateEntry'));
        dispatch_post('/entry/:id/update', array($this, 'updateEntry'));
        dispatch_post('/entry/:id/delete', array($this, 'deleteEntry')); 

        // GET actions
        dispatch_get(array('/entry/list.*', array('format')), array($this, 'showList'));
        dispatch_get('/entry/new', array($this, 'newEntry'));       
        dispatch_get('/entry/:id', array($this, 'showEntry'));
        dispatch_get('/entry/:id/edit', array($this, 'editEntry'));
    }

    public function showList() {
        $oView = new EntryListView();
        $oInput = Input::getInstance();

        $iSkip = (int)$oInput->getGetData('skip');
        $iLimit = (int)$oInput->getGetData('limit');
        $iType = (int)$oInput->getGetData('type');
        $sTerm = $oInput->getGetData('term');
        $sSort = $oInput->getGetData('sort');
        $sDirection = $oInput->getGetData('direction');
        $sFormat = $oInput->sanitize(params('format'));
        
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

        $oView->render();    
    }

    public function showEntry() {
        $sId = Input::getInstance()->sanitize(params('id'));

        $oView = new EntryView($sId);
        $oView->render();
    }

    public function newEntry() {
        $oView = new EntryNewView();
        $oView->render(); 
    }

    public function editEntry() {
        $sId = Input::getInstance()->sanitize(params('id'));

        $oView = new EntryEditView($sId);
        $oView->render();        
    }

    public function updateEntry() {
        $sId = params('id');
        $entryData = Input::getInstance()->getData('data');

        $oView = new EntryEditView($sId);
        $oDocument = $oView->getDocument();
        $oDocument->updateProperties($entryData);
        $oDocument->save();

        try {
            $iTypeId = $oView->getTypeId();
        } catch(\Exception $e) {
            $iTypeId = null;
        }

        if((isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json')
            || Input::getInstance()->getRequestMethod() === 'PUT') {
            $oView->renderJsonUpdateResponse();
        } else {
            $oView->redirect('/entry/list.html', array('type' => $iTypeId));
        }          
    }

    public function deleteEntry() {
        $sId = params('id');
        $oView = new EntryEditView($sId);
        $oView->getDocument()->delete();

        if((isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json')
            || Input::getInstance()->getRequestMethod() === 'DELETE') {
            $oView->renderJsonDeleteResponse();
        } else {            
            $oView->redirect('/entry/list.html', array('type' => $iTypeId));
        }
    }
}