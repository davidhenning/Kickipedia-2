<?php

namespace Kickipedia2\Models;

use MongoAppKit\Documents\Document;

class BaseDocument extends Document {
    public function getPreparedProperties() {
        $this->_properties['updatedOn'] = new \MongoDate();

        return parent::getPreparedProperties();
    }
}