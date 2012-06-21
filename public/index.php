<?php

session_start();

require_once(__DIR__ . '/../bootstrap.php');

use MongoAppKit\HttpAuthDigest,
    MongoAppKit\Config,
    MongoAppKit\Input,
    MongoAppKit\Exceptions\HttpException;

use Kickipedia2\Controllers\EntryActions,
    Kickipedia2\Models\UserDocumentList;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Silex\Application,
    Silex\Provider\UrlGeneratorServiceProvider; 

try {
    Config::getInstance()->addConfigFile('kickipedia2.json');

    $oApp = new Application();
    $oApp->register(new UrlGeneratorServiceProvider());
    #$oApp['debug'] = true;

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