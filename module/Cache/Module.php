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

    public function bootstrap($e)
    {
        $app = $e->getParam('application');
        $di  = $app->getLocator();
        if (!$di instanceof Di) {
            return;
        }

        $config  = $e->getParam('config');
        $di->instanceManager()->setParameters('Cache\Listener', array(
            'cache' => function() use ($config) {
                if (!isset($config['cache'])) {
                    throw new RuntimeException('Unable to instantiate cache; missing cache key in config');
                }
                $cacheConfig = $config['cache'];
                if ($cacheConfig instanceof Traversable) {
                    $cacheConfig = IteratorToArray::convert($cacheConfig);
                }
                if (!isset($cacheConfig['frontend'])
                    || !isset($cacheConfig['backend'])
                    || !isset($cacheConfig['frontend_options'])
                    || !isset($cacheConfig['backend_options'])
                ) {
                    throw new RuntimeException('Cache configuration missing one or more of the following keys: frontend, backend, frontend_options, backend_options');
                }
                $cache = Cache::factory(
                    $cacheConfig['frontend'],
                    $cacheConfig['backend'],
                    $cacheConfig['frontend_options'],
                    $cacheConfig['backend_options']
                );
                return $cache;
            }
        ));
        $listener = $di->get('Cache\Listener');
        $app->events()->attachAggregate($listener);
    }
}
