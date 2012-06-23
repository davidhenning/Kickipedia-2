<?php

namespace Kickipedia2\Models;

class EntryDocument extends BaseDocument {

    public function __construct() {
        $this->setDatabase($this->getStorage()->getDatabase());
        $this->setConfig($this->getConfig());
        $this->setCollectionName('entry');
    }

    public function getUrl() {
        return "/entry/{$this->getId()}";
    }

    public function getEditUrl() {
        return "/entry/{$this->getId()}/edit";
    }

    public function getDeleteUrl() {
        return "/entry/{$this->getId()}/delete";
    }
}