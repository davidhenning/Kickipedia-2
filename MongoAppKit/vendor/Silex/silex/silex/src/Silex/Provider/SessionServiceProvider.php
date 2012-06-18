<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\FileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Symfony HttpFoundation component Provider for sessions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SessionServiceProvider implements ServiceProviderInterface
{
    private $app;

    public function register(Application $app)
    {
        $this->app = $app;

        $app['session.test'] = false;

        $app['session'] = $app->share(function () use ($app) {
            if (!isset($app['session.storage'])) {
                if ($app['session.test']) {
                    $app['session.storage'] = $app['session.storage.test'];
                } else {
                    $app['session.storage'] = $app['session.storage.native'];
                }
            }

            return new Session($app['session.storage']);
        });

        $app['session.storage.handler'] = $app->share(function () use ($app) {
            return new FileSessionHandler($app['session.storage.save_path']);
        });

        $app['session.storage.native'] = $app->share(function () use ($app) {
            return new NativeSessionStorage(
                $app['session.storage.options'],
                $app['session.storage.handler']
            );
        });

        $app['session.storage.test'] = $app->share(function() {
            return new MockFileSessionStorage();
        });

        $app['session.storage.options'] = array();
        $app['session.default_locale'] = 'en';
        $app['session.storage.save_path'] = null;
    }

    public function onEarlyKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $request->setSession($this->app['session']);

        // starts the session if a session cookie already exists in the request...
        if ($request->hasPreviousSession()) {
            $request->getSession()->start();
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        // bootstrap the session
        if (!isset($this->app['session'])) {
            return;
        }

        $session = $this->app['session'];
        $cookies = $event->getRequest()->cookies;

        if ($cookies->has($session->getName())) {
            $session->setId($cookies->get($session->getName()));
        } else {
            $session->migrate(false);
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if ($session = $event->getRequest()->getSession()) {
            $session->save();

            $params = session_get_cookie_params();

            $event->getResponse()->headers->setCookie(new Cookie($session->getName(), $session->getId(), 0 === $params['lifetime'] ? 0 : time() + $params['lifetime'], $params['path'], $params['domain'], $params['secure'], $params['httponly']));
        }
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addListener(KernelEvents::REQUEST, array($this, 'onEarlyKernelRequest'), 128);

        if ($app['session.test']) {
            $app['dispatcher']->addListener(KernelEvents::REQUEST, array($this, 'onKernelRequest'), 192);
            $app['dispatcher']->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'), -128);
        }
    }
}
