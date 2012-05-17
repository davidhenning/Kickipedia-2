<?php

$time = microtime(true);

require_once(__DIR__ . '/../bootstrap.php');

use Kickipedia2\Controllers\EntryActions;
use MongoAppKit\HttpAuthDigest;
use MongoAppKit\Exceptions\HttpException;
use Kickipedia2\Models\UserDocumentList;

try {
    $sDigest = (isset($_SERVER['PHP_AUTH_DIGEST'])) ? $_SERVER['PHP_AUTH_DIGEST'] : null;

    if(empty($sDigest) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $sDigest = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
        
    $oAuth = new HttpAuthDigest('Kickipedia2', $sDigest);
    $oAuth->sendAuthenticationHeader();
    $oUserList = new UserDocumentList();
    $sUserName = $oAuth->getUserName();
    $aUserDocument = $oUserList->getUser($sUserName);

    $oAuth->authenticate($aUserDocument->getProperty('token'));
} catch(HttpException $e) {
    if($e->getCode() === 401) {
        $oAuth->sendAuthenticationHeader(true);
    }

    echo $e->getMessage();
    exit();
} catch(\Exception $e) {
    echo $e->getMessage();
    exit();
}

$entryActions = new EntryActions();

run();

#echo sprintf("%01.6f", microtime(true) - $time).'s, max memory usage: '.(memory_get_peak_usage() / 1024 / 1024).' MB';