<?php

session_start();

$time = microtime(true);

require_once(__DIR__ . '/../bootstrap.php');

use MongoAppKit\HttpAuthDigest;
use MongoAppKit\Config;
use MongoAppKit\Input;
use MongoAppKit\Exceptions\HttpException;
use Kickipedia2\Controllers\EntryActions;
use Kickipedia2\Models\UserDocumentList;

try {
    Config::getInstance()->addConfigFile('kickipedia2.json');

    $sDigest = (isset($_SERVER['PHP_AUTH_DIGEST'])) ? $_SERVER['PHP_AUTH_DIGEST'] : null;

    if(empty($sDigest) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $sDigest = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
        
    $oAuth = new HttpAuthDigest('Kickipedia2', $sDigest);
    $oAuth->sendAuthenticationHeader();
    $oUserList = new UserDocumentList();
    $sUserName = Input::getInstance()->sanitize($oAuth->getUserName());
    $aUserDocument = $oUserList->getUser($sUserName);
    $_SESSION['user'] = $aUserDocument;

    $oAuth->authenticate($aUserDocument->getProperty('token'));
} catch(HttpException $e) {
    if($e->getCode() === 401) {
        $oAuth->sendAuthenticationHeader(true);
    }

    include_once('./error.php');
    exit();
} catch(\Exception $e) {
    include_once('./error.php');
    exit();
}

try {
    $oApp = new Silex\Application(); 

    $entryActions = new EntryActions($oApp);

    $oApp->run();
} catch(\InvalidArgumentException $e) {
    include_once('./error.php');
    exit();
} catch(\Exception $e) {
    include_once('./error.php');
    exit();
}

#echo sprintf("%01.6f", microtime(true) - $time).'s, max memory usage: '.(memory_get_peak_usage() / 1024 / 1024).' MB';