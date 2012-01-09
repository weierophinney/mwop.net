<?php

namespace Authentication;

use InvalidArgumentException,
    Zend\Config\Config,
    Zend\Di\Locator,
    Zend\EventManager\StaticEventmanager,
    Zend\Module\Consumer\AutoloaderProvider;

class Module implements AutoloaderProvider
{
    public function init()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'bootstrap'));
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function bootstrap($e)
    {
        $app      = $e->getParam('application');
        $config   = $e->getParam('config');
        $locator  = $app->getLocator();
        $events   = StaticEventManager::getInstance();
        $listener = $locator->get('Authentication\AuthenticationListener', array('config' => $config));
        $events->attach('Zend\Stdlib\Dispatchable', 'dispatch', array($listener, 'testAuthenticatedUser'), 100);
        $events->attach('Zend\Stdlib\Dispatchable', 'authenticate', array($listener, 'testAuthenticatedUser'), 100);
        $this->registerCacheRules();
    }

    protected function registerCacheRules()
    {
        if (!class_exists('Module\Cache', false)) {
            return;
        }

        $cacheListener = $locator->get('Cache\Listener');
        $cacheListener->addRule(function($e) {
            if (!$e instanceof \Zend\Mvc\MvcEvent) {
                return;
            }

            $routeMatch = $e->getRouteMatch();
            if (in_array($routeMatch->getMatchedRouteName(), array('authentication-login', 'authentication-logout'))) {
                // Do not cache authentication requests
                return true;
            }
            return false;
        });
    }
}
