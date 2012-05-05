<?php

namespace Kickipedia2\Controllers;

use Kickipedia2\Views\EntryView;
use Kickipedia2\Views\EntryEditView;

class EntryActions {

    public function __construct() {
        dispatch('/entry/:id', array($this, 'showEntry'));
        dispatch('/entry/:id/edit', array($this, 'editEntry'));
        dispatch_post('/entry/:id/update', array($this, 'updateEntry'));
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

        redirect_to('entry', $sId, 'edit');
    }
}