<?php

namespace Kickipedia2\Models;

class EntryDocument extends BaseDocument {
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