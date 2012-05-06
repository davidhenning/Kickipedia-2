<?php

namespace Kickipedia2\Controllers;

use Kickipedia2\Views\EntryListView;
use Kickipedia2\Views\EntryView;
use Kickipedia2\Views\EntryEditView;

class EntryActions {

    public function __construct() {
        dispatch('/entry/list', array($this, 'showList'));
        dispatch('/entry/list/:page', array($this, 'showList'));
        dispatch('/entry/list/type/:type', array($this, 'showList'));
        dispatch('/entry/list/type/:type/:page', array($this, 'showList'));
        dispatch('/entry/:id', array($this, 'showEntry'));
        dispatch('/entry/:id/edit', array($this, 'editEntry'));
        dispatch_post('/entry/:id/update', array($this, 'updateEntry'));
    }

    public function showList() {
        $oView = new EntryListView();
 
        $iPage = (int)params('page');
        $iType = (int)params('type');
        
        if($iPage > 0) {
            $oView->setPage($iPage);
        }

        if($iType > 0) {
            $oView->setType($iType);
        }

        $oView->setPerPage(100);

        $oView->render();    
    }

    public function showEntry() {
        $sId = params('id');

        $oView = new EntryView($sId);
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

        redirect_to('entry', 'list');
    }
}