<?php

namespace Kickipedia2\Models;

class UserDocument extends BaseDocument {
    
    public function __construct() {
        parent::__construct();

        $this->setCollectionName('user');
    }
}