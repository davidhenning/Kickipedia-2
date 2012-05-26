<?php

namespace Kickipedia2\Models;

use MongoAppKit\Documents\Document;

class UserDocument extends Document {
    
    public function __construct() {
        parent::__construct();

        $this->setCollectionName('user');
    }

}