<?php

session_start();

require_once(__DIR__ . '/../bootstrap.php');

use MongoAppKit\Config,
    MongoAppKit\HttpAuthDigest, 
    MongoAppKit\Exceptions\HttpException;

use Kickipedia2\Controllers\EntryActions,
    Kickipedia2\Models\UserDocumentList;

use Symfony\Component\HttpFoundation\Request;

use Silex\Application; 

try {
    $oConfig = Config::getInstance();
    $oConfig->addConfigFile('kickipedia2.json');
    $oRequest = $oConfig->getRequest();

    $oApp = new Application();
    $oApp['debug'] = $oConfig->getProperty('DebugMode');

    $oApp->before(function(Request $oRequest) {
        if(strpos($oRequest->headers->get('Content-Type'), 'application/json') === 0) {
            $aData = json_decode($oRequest->getContent(), true);
            $oRequest->request->replace(is_array($aData) ? $aData : array());
        }
    });
    
    $sDigest = $oRequest->server->get('PHP_AUTH_DIGEST');
    $sHttpAuth = $oRequest->server->get('REDIRECT_HTTP_AUTHORIZATION');
    
    if(empty($sDigest) && !empty($sHttpAuth)) {
        $sDigest = $sHttpAuth;
    }
        
    $oAuth = new HttpAuthDigest('Kickipedia2', $sDigest);
    $oAuth->sendAuthenticationHeader();
    $oUserList = new UserDocumentList();
    $sUserName = $oConfig->sanitize($oAuth->getUserName());
    $aUserDocument = $oUserList->getUser($sUserName);
    $_SESSION['user'] = $aUserDocument;

    $oAuth->authenticate($aUserDocument->getProperty('token'));

    $entryActions = new EntryActions($oApp);
    $oApp->run();
} catch(HttpException $e) {
    if($e->getCode() === 401) {
        $oAuth->sendAuthenticationHeader(true);
    }

    include_once('./error.php');
    exit();
} catch(\InvalidArgumentException $e) {
    include_once('./error.php');
    exit();
} catch(\Exception $e) {
    include_once('./error.php');
    exit();
}