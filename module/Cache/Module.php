<?php

namespace Cache;

use RuntimeException,
    Traversable,
    Zend\Cache\Cache,
    Zend\Di\Di,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\Stdlib\IteratorToArray;

class Module implements AutoloaderProvider
{
    public function init($manager)
    {
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'bootstrap'), 100);
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
        $app = $e->getParam('application');
        $di  = $app->getLocator();
        if (!$di instanceof Di) {
            return;
        }

        $listener = $di->get('Cache\Listener');
        $app->events()->attachAggregate($listener);
    }
}
