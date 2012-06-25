<?php

session_start();

require_once(__DIR__ . '/../bootstrap.php');

use MongoAppKit\Config,
    MongoAppKit\Storage,
    MongoAppKit\HttpAuthDigest, 
    MongoAppKit\Exceptions\HttpException;

use Kickipedia2\Controllers\EntryActions,
    Kickipedia2\Models\UserDocumentList;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Silex\Application; 


$oApp = new Application();
$oConfig = new Config();
$oConfig->addConfigFile('mongoappkit.json');
$oConfig->addConfigFile('kickipedia2.json');
$oStorage = new Storage($oConfig);
$oConfig->setProperty('storage', $oStorage);
Request::trustProxyData();

$oApp['debug'] = $oConfig->getProperty('DebugMode');
$oApp['config'] = $oConfig;

$oApp->before(function(Request $oRequest) use($oConfig) {
    if(strpos($oRequest->headers->get('Content-Type'), 'application/json') === 0) {
        $aData = json_decode($oRequest->getContent(), true);
        $oRequest->request->replace(is_array($aData) ? $aData : array());
    }
});

$oApp->before(function(Request $oRequest) use($oConfig) {      
    $oAuth = new HttpAuthDigest($oRequest, 'Kickipedia2'); 
    $oResponse = $oAuth->sendAuthenticationHeader();

    if($oResponse instanceof Response) {
        return $oResponse;
    }

    $oUserList = new UserDocumentList($oConfig);
    $sUserName = $oConfig->sanitize($oAuth->getUserName());
    $aUserDocument = $oUserList->getUser($sUserName);
    $_SESSION['user'] = $aUserDocument;

    $oAuth->authenticate($aUserDocument->getProperty('token'));
});

$oApp->error(function(HttpException $e, $code) use($oApp) {
    if($e->getCode() === 401) {
        return $e->getCallingObject()->sendAuthenticationHeader(true);
    }

    return new Response('Kickipedia error: '.$e->getMessage(), $e->getCode());
});

$oApp->error(function(Exception $e, $code) use($oApp) {
    $oRequest = $oApp['request'];

    if(strpos($oRequest->headers->get('Content-Type'), 'application/json') === 0) {
        $aError = array(
            'status' => $e->getCode(),
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => $oRequest->getMethod(),
                'url' => $oRequest->getPathInfo()
            ),
            'response' => array(
                'error' => str_ireplace('exception', '', get_class($e)),
                'message' => $e->getMessage(),
                'backtrace' => $e->getTrace(),
                'server' => $_SERVER
            )
        );

        return $oApp->json($aError, 400);
    }
});

$entryActions = new EntryActions($oApp);
$oApp->run();