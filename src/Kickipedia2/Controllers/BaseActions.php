<?php

namespace Kickipedia2\Controllers;

use MongoAppKit\Config;

use Silex\Application;

abstract class BaseActions {

    protected $_oConfig = null;
    protected $_oApp = null;

    public function __construct(Application $oApp) {
        $this->_oApp = $oApp;
        $this->_initRoutes();
    }

    abstract protected function _initRoutes();
}