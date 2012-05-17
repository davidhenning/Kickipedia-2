<?php

namespace Kickipedia2\Views;

class EntryNewView extends EntryEditView {

    protected $_sTemplateName = 'entry_new.twig';
    protected $_bShowEditTools = false;

    public function getInsertUrl() {
        return url_for('entry', 'insert');
    }
}