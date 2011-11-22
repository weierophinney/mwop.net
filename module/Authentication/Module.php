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
        $events->attach('bootstrap', 'bootstrap', array($this, 'registerStaticListeners'));
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
        );
    }

    public function getConfig($env = null)
    {
        $config = include __DIR__ . '/config/module.config.php';
        if (null === $env) {
            return $config;
        }

        if (!isset($config[$env])) {
            throw new InvalidArgumentException(sprintf(
                'Unrecognized environment "%s" provided to %s',
                $env,
                __METHOD__
            ));
        }

        return $config[$env];
    }

    public function registerStaticListeners($e)
    {
        $app      = $e->getParam('application');
        $config   = $e->getParam('config');
        $locator  = $app->getLocator();
        $events   = StaticEventManager::getInstance();
        $listener = $locator->get('Authentication\AuthenticationListener', array('config' => $config));
        $events->attach('Zend\Stdlib\Dispatchable', 'dispatch', array($listener, 'testAuthenticatedUser'), 100);
        $events->attach('Zend\Stdlib\Dispatchable', 'authenticate', array($listener, 'testAuthenticatedUser'), 100);
    }
}
