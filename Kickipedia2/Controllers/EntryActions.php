<?php

namespace Kickipedia2\Controllers;

use Kickipedia2\Views\EntryView;

class EntryActions {

    public function __construct() {
        dispatch('/entry/:id', array($this, 'showEntry'));
    }

    public function showEntry() {
        $id = params('id');

        $view = new EntryView($id);
        $view->render();
    }
}