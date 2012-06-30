<?php

namespace Kickipedia2\Controllers;

use MongoAppKit\Config;

use Silex\Application;

abstract class BaseActions {

    public function __construct(Application $oApp) {
        $this->_initRoutes($oApp);
    }

    abstract protected function _initRoutes(Application $oApp);
}