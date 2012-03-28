<?php

namespace PhlyBlog;

use Traversable,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\Stdlib\ArrayUtils;

class Module implements AutoloaderProvider
{
    public static $config;

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

    public function init($manager)
    {
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'onBootstrap'));
    }

    public function onBootstrap($e)
    {
        self::$config = $e->getParam('config');
        if (self::$config instanceof Traversable) {
            self::$config = ArrayUtils::iteratorToArray(self::$config);
        }
    }

    public static function prepareCompilerView($view, $config, $locator)
    {
        $renderer = $locator->get('Zend\View\Renderer\PhpRenderer');
        $view->addRenderingStrategy(function($e) use ($renderer) {
            return $renderer;
        }, 100);
    }
}
