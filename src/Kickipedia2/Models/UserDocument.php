<?php

namespace Kickipedia2\Models;

use MongoAppKit\Config;

use \Phpass\Hash,
    \Phpass\Hash\Adapter\Pbkdf2;

use Silex\Application;

class UserDocument extends BaseDocument {
    
    public function __construct(Application $app) {
        parent::__construct($app);
        $this->setCollectionName('user');
    }

    public function setPassword($password) {
        try {
            $adapter = new Pbkdf2(array (
                'iterationCountLog2' => 16
            ));

            $hash = new Hash($adapter);
            $passwordHash = $hash->hashPassword($password);
            $this->setProperty('password', $passwordHash);
        } catch(\Exception $e) {

        }
    }
}