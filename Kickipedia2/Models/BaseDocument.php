<?php

namespace Kickipedia2\Models;

use MongoAppKit\Documents\Document;

class BaseDocument extends Document {

    public function __construct() {
        parent::__construct();
    }

    public function getPreparedProperties() {
        $this->_aProperties['updatedOn'] = new \MongoDate();

        return parent::getPreparedProperties();
    }

}