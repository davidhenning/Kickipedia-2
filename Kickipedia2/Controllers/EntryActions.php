<?php

namespace Kickipedia2\Controllers;

use Kickipedia2\Views\EntryList;
use Kickipedia2\Views\EntryView;
use Kickipedia2\Views\EntryEditView;

class EntryActions {

    public function __construct() {
        dispatch('/entry/list', array($this, 'showList'));
        dispatch('/entry/:id', array($this, 'showEntry'));
        dispatch('/entry/:id/edit', array($this, 'editEntry'));
        dispatch_post('/entry/:id/update', array($this, 'updateEntry'));
    }

    public function showList() {
        $oView = new EntryList();
        $oView->render();    }

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

        redirect_to('entry', $sId, 'edit');
    }
}