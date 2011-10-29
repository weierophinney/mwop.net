<?php

namespace Authentication;

use InvalidArgumentException,
    Zend\Config\Config,
    Zend\Di\Locator,
    Zend\EventManager\StaticEventmanager,
    Zend\Loader\AutoloaderFactory;

class Module
{
    public function init()
    {
        $this->initAutoloader();
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'registerStaticListeners'));
    }

    public function initAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
        ));
    }

    public function getConfig($env = null)
    {
        $config = new Config(include __DIR__ . '/configs/module.config.php');
        if (null === $env) {
            return $config;
        }

        if (!isset($config->{$env})) {
            throw new InvalidArgumentException(sprintf(
                'Unrecognized environment "%s" provided to %s',
                $env,
                __METHOD__
            ));
        }

        return $config->{$env};
    }

    public function registerStaticListeners($e)
    {
        $app      = $e->getParam('application');
        $modules  = $e->getParam('modules');
        $locator  = $app->getLocator();
        $config   = $modules->getMergedConfig();
        $events   = StaticEventManager::getInstance();
        $listener = $locator->get('Authentication\AuthenticationListener', array('config' => $config));
        $events->attach('Zend\Stdlib\Dispatchable', 'dispatch', array($listener, 'testAuthenticatedUser'), 100);
        $events->attach('Zend\Stdlib\Dispatchable', 'authenticate', array($listener, 'testAuthenticatedUser'), 100);
    }
}
