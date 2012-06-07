<?php

namespace Kickipedia2;

class Loader {
	public static function registerAutoloader() {
		return spl_autoload_register(array ('Kickipedia2\\Loader', 'load'));
	}

	public static function load($sClassName) {
        if(substr($sClassName, 0, 11) !== 'Kickipedia2') {
            return;
        }

        $sLibraryRoot = realpath(__DIR__ . '/../');
        $sFileName = str_replace('\\', DIRECTORY_SEPARATOR, $sClassName) . '.php';
        $sFileName = realpath($sLibraryRoot . DIRECTORY_SEPARATOR . $sFileName);
        
        if(substr($sFileName, 0, strlen($sLibraryRoot)) == $sLibraryRoot) {
            
            if(is_readable($sFileName)) {
                require_once($sFileName);
            }
        }
	}
}