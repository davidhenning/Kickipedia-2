<?php

namespace Kickipedia2\Models;

use MongoAppKit\Documents\Document;

class EntryDocument extends Document {

    public function __construct() {
        parent::__construct();

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