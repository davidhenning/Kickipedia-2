<?php

namespace Kickipedia2\Models;

use MongoAppKit\Config;

class EntryDocument extends BaseDocument {

    public function __construct(Config $oConfig) {
        $oDatabase = $oConfig->getProperty('storage')->getDatabase();

        $this->setDatabase($oConfig->getProperty('storage')->getDatabase());
        $this->setConfig($oConfig);
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