<?php

namespace Kickipedia2\Models;

use MongoAppKit\Config;

use \Phpass\Hash,
    \Phpass\Hash\Adapter\Pbkdf2;

use Silex\Application;

class UserDocument extends BaseDocument {
    
    public function __construct(Application $oApp) {
        parent::__construct($oApp);
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