<?php

namespace Kickipedia2\Controllers;

use Kickipedia2\Views\EntryListView;
use Kickipedia2\Views\EntryView;
use Kickipedia2\Views\EntryEditView;
use Kickipedia2\Views\EntryNewView;

class EntryActions {

    public function __construct() {
        dispatch_get('/entry/new', array($this, 'newEntry'));
        dispatch_get('/entry/list', array($this, 'showList'));
        dispatch_get('/entry/list/:page', array($this, 'showList'));
        dispatch_get('/entry/list/type/:type', array($this, 'showList'));
        dispatch_get('/entry/list/type/:type/:page', array($this, 'showList'));
        
        dispatch_get('/entry/:id', array($this, 'showEntry'));
        dispatch_get('/entry/:id/edit', array($this, 'editEntry'));
               
        dispatch_post('/entry/insert', array($this, 'updateEntry'));
        dispatch_post('/entry/:id/update', array($this, 'updateEntry'));
        dispatch_delete('/entry/:id/delete', array($this, 'deleteEntry'));
    }

    public function showList() {
        $oView = new EntryListView();
 
        $iPage = (int)params('page');
        $iType = (int)params('type');
        
        if($iPage > 0) {
            $oView->setCurrentPage($iPage);
        }

        if($iType > 0) {
            $oView->setType($iType);
        }

        $oView->setPerPage(100);

        if(isset($_GET['output'])) {
            $oView->setOutputMethod($_GET['output']);
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
        $entryData = $_POST['entry'];

        $oView = new EntryEditView($sId);
        $oView->update($entryData);

        try {
            $iTypeId = $oView->getTypeId();
        } catch(\Exception $e) {
            $iTypeId = null;
        }

        redirect_to('entry', 'list', 'type', $iTypeId);
    }

    public function deleteEntry() {
        $sId = params('id');
        $oView = new EntryEditView($sId);
        $oView->delete();
    }
}