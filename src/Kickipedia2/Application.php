<?php

namespace Kickipedia2;

use Silex\Application as SilexApplication;

use MongoAppKit\Config,
    MongoAppKit\Storage;

class Application extends SilexApplication {
	
	public function __construct(Config $oConfig, Storage $oStorage) {
		parent::__construct();

		$this['debug'] = $oConfig->getProperty('DebugMode');
		$this['config'] = $oConfig;
		$this['storage'] = $oStorage;
	}
}