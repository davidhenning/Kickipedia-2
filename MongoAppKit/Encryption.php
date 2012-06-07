<?php

namespace MongoAppKit;

class Encryption {

    /**
     * Config object
     * @var Config
     */

    private static $_oInstance = null;

    /**
     * Return instance of class Config
     *
     * @return Config
     */

    public static function getInstance() {
        if(self::$_oInstance === null) {
            self::$_oInstance = new Encryption();
        }

        return self::$_oInstance;
    }

    /**
     * Constructor
     */

    private function __construct() {

    }

    /**
     * Prohibit cloning of the class object (Singleton pattern)
     */

    public function __clone() {
        return null;
    }

	public function encrypt($value, $key) {
		return \GibberishAES::enc($value, $key);
	}

	public function decrypt($value, $key) {
		return \GibberishAES::dec($value, $key);
	}
}