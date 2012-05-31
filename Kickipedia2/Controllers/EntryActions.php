<?php

namespace Kickipedia2\Controllers;


use MongoAppKit\Input;
use Kickipedia2\Views\EntryListView;
use Kickipedia2\Views\EntryView;
use Kickipedia2\Views\EntryEditView;
use Kickipedia2\Views\EntryNewView;

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
        dispatch_get(array('/entry/search.*', array('format')), array($this, 'showList'));
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
        $sId = params('id');

        $oView = new EntryView($sId);
        $oView->render();
    }

    public function newEntry() {
        $oView = new EntryNewView();
        $oView->render(); 
    }

    public function editEntry() {
        $sId = params('id');

        $oView = new EntryEditView($sId);
        $oView->render();        
    }

    public function updateEntry() {
        $sId = params('id');
        $entryData = Input::getInstance()->getData('entry');

        $oView = new EntryEditView($sId);
        $oView->update($entryData);

        try {
            $iTypeId = $oView->getTypeId();
        } catch(\Exception $e) {
            $iTypeId = null;
        }

        if(Input::getInstance()->getRequestMethod() === 'POST') {
            redirect_to('entry', 'list', 'type', $iTypeId);
        }  elseif(Input::getInstance()->getRequestMethod() === 'PUT') {
            $oView->renderPutResponse();
        }        
    }

    public function deleteEntry() {
        $sId = params('id');
        $oView = new EntryEditView($sId);
        $oView->delete();

        if(Input::getInstance()->getRequestMethod() === 'POST') {
            redirect_to('entry', 'list', 'type', $iTypeId);
        }  elseif(Input::getInstance()->getRequestMethod() === 'DELETE') {
            $oView->renderDeleteResponse();
        } 
    }
}