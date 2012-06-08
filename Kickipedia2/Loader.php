<?php

namespace Kickipedia2;

$sDependencyFile = __DIR__ . '/vendor/dependencies.json';

if(file_exists($sDependencyFile)) {
    $sDependencies = file_get_contents($sDependencyFile);
    $aDependencies = json_decode($sDependencies, true);

    if(!empty($aDependencies)) {
        foreach($aDependencies as $aDependency) {
            $sDependencyFile = __DIR__ . '/vendor/' . $aDependency['file'];
            if(file_exists($sDependencyFile)) {
                include_once($sDependencyFile);

                if(!empty($aDependency['autoload'])) {
                    if(isset($aDependency['autoload']['className']) && isset($aDependency['autoload']['methodName'])) {
                        call_user_func(array($aDependency['autoload']['className'], $aDependency['autoload']['methodName']));
                    }
                }
            }
        }
    }
}

class Loader {
	
    public static function registerAutoloader() {
		return spl_autoload_register(array ('Kickipedia2\\Loader', 'load'));
	}

	public static function load($sClassName) {
        if(substr($sClassName, 0, 11) !== 'Kickipedia2') {
            return;
        }

        $sLibraryRoot = realpath(__DIR__ . '/../');
        $sFileName = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $sClassName) . '.php';
        $sFileName = realpath($sLibraryRoot . DIRECTORY_SEPARATOR . $sFileName);
        
        if(substr($sFileName, 0, strlen($sLibraryRoot)) == $sLibraryRoot) {
            
            if(is_readable($sFileName)) {
                include_once($sFileName);
            }
        }
	}

}