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
	
	public function __construct(Config $config) {
		parent::__construct($config);

		$app = $this;

		$this['debug'] = $config->getProperty('DebugMode');

        $this->register(new SessionServiceProvider());

		$this->before(function(Request $request) use($config) {
		    if(strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
		        $data = json_decode($request->getContent(), true);
		        $request->request->replace(is_array($data) ? $data : array());
		    }
		});

		$this->before(function(Request $request) use($app, $config) {
		    $auth = new HttpAuthDigest($request, 'Kickipedia2');
		    $response = $auth->sendAuthenticationHeader();

		    if($response instanceof Response) {
		        return $response;
		    }

		    $userList = new UserDocumentList($app);
		    $userName = $config->sanitize($auth->getUserName());
		    $userDocument = $userList->getUser($userName);
            $app['session']->set('user', $userDocument->getProperties());

		    $auth->authenticate($userDocument->getProperty('token'));
		});

		$this->error(function(HttpException $e) use($app) {
		    if($e->getCode() === 401) {
		        return $e->getCallingObject()->sendAuthenticationHeader(true);
		    }

		    $exceptionHandler = new ExceptionHandler($app['config']);

		    return $exceptionHandler->createResponse($e);
		});

		$this->error(function(\Exception $e) use($app) {
		    $request = $app['request'];

		    $log = new Logger('name');
		    $log->pushHandler(new StreamHandler(getBasePath() . 'logs/error.log', Logger::WARNING));

		    $error = array(
		        'status' => $e->getCode(),
		        'time' => date('Y-m-d H:i:s'),
		        'request' => array(
		            'method' => $request->getMethod(),
		            'url' => $request->getPathInfo()
		        ),
		        'response' => array(
		            'error' => str_ireplace('exception', '', get_class($e)),
		            'message' => $e->getMessage(),
		            'backtrace' => $e->getTrace(),
		            'server' => $_SERVER
		        )
		    );

		    $log->addError("{$request->getMethod()} {$request->getPathInfo()} - HTTP response {$e->getCode()}: {$e->getMessage()}");

		    if(strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
		        return $app->json($error, 400);
		    }

		    $exceptionHandler = new ExceptionHandler($app['config']);

		    return $exceptionHandler->createResponse($e);
		});
	}
}