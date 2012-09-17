<?php

namespace Kickipedia2;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpKernel\Debug\ExceptionHandler;

use Silex\Provider\SessionServiceProvider;

use MongoAppKit\Application as MongoAppKitApplication,
    MongoAppKit\Config,
    MongoAppKit\HttpAuthDigest, 
    MongoAppKit\Exceptions\HttpException;

use Kickipedia2\Models\UserDocumentList;

use Monolog\Logger,
    Monolog\Handler\StreamHandler;

class Application extends MongoAppKitApplication {
	
	public function __construct(Config $oConfig) {
		parent::__construct($oConfig);

		$oApp = $this;

		$this['debug'] = $oConfig->getProperty('DebugMode');

        $this->register(new SessionServiceProvider());

		$this->before(function(Request $oRequest) use($oConfig) {
		    if(strpos($oRequest->headers->get('Content-Type'), 'application/json') === 0) {
		        $aData = json_decode($oRequest->getContent(), true);
		        $oRequest->request->replace(is_array($aData) ? $aData : array());
		    }
		});

		$this->before(function(Request $oRequest) use($oApp, $oConfig) {
		    $oAuth = new HttpAuthDigest($oRequest, 'Kickipedia2');
		    $oResponse = $oAuth->sendAuthenticationHeader();

		    if($oResponse instanceof Response) {
		        return $oResponse;
		    }

		    $oUserList = new UserDocumentList($oApp);
		    $sUserName = $oConfig->sanitize($oAuth->getUserName());
		    $aUserDocument = $oUserList->getUser($sUserName);
            $oApp['session']->set('user', $aUserDocument->getProperties());

		    $oAuth->authenticate($aUserDocument->getProperty('token'));
		});

		$this->error(function(HttpException $e) use($oApp) {
		    if($e->getCode() === 401) {
		        return $e->getCallingObject()->sendAuthenticationHeader(true);
		    }

		    $oExceptionHandler = new ExceptionHandler($oApp['config']);

		    return $oExceptionHandler->createResponse($e);
		});

		$this->error(function(\Exception $e) use($oApp) {
		    $oRequest = $oApp['request'];

		    $oLog = new Logger('name');
		    $oLog->pushHandler(new StreamHandler(getBasePath() . 'logs/error.log', Logger::WARNING));

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

		    $oLog->addError("{$oRequest->getMethod()} {$oRequest->getPathInfo()} - HTTP response {$e->getCode()}: {$e->getMessage()}");

		    if(strpos($oRequest->headers->get('Content-Type'), 'application/json') === 0) {
		        return $oApp->json($aError, 400);
		    }

		    $oExceptionHandler = new ExceptionHandler($oApp['config']);

		    return $oExceptionHandler->createResponse($e);
		});
	}
}