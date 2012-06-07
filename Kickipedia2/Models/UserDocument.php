<?php

namespace Kickipedia2\Models;

use \Phpass\Hash,
    \Phpass\Hash\Adapter\Pbkdf2;

class UserDocument extends BaseDocument {
    
    public function __construct() {
        parent::__construct();

        $this->setCollectionName('user');
    }

    public function setPassword($sPassword) {
        try {
            $oAdapter = new Pbkdf2(array (
                'iterationCountLog2' => 16
            ));

            $oHash = new Hash($oAdapter);
            $sHash = $oHash->hashPassword($sPassword);
            $this->setProperty('password', $sHash);
        } catch(\Exception $e) {

        }
    }
}