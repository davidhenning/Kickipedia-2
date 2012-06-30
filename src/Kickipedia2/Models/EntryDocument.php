<?php

namespace Kickipedia2\Models;

use MongoAppKit\Config;

use Silex\Application;

class EntryDocument extends BaseDocument {

    public function __construct(Application $oApp) {
        parent::__construct($oApp);
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