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
        $id = params('id');

        $view = new EntryView($id);
        $view->render();
    }

    public function editEntry() {
        $id = params('id');

        $view = new EntryEditView($id);
        $view->render();        
    }

    public function updateEntry() {
        $id = params('id');
        $entryData = $_POST['entry'];

        $view = new EntryEditView($id);
        $view->update($entryData);

        redirect_to('entry', $id, 'edit');
    }
}