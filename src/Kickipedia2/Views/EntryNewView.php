<?php

namespace Kickipedia2\Views;

class EntryNewView extends EntryEditView {

    protected $_templateName = 'entry_new.twig';
    protected $_showEditTools = false;

    public function getInsertUrl() {
        return '/entry/insert';
    }
}