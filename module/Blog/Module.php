<?php

namespace Blog;

use Traversable,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\Stdlib\ArrayUtils,
    Zend\View\Model\ViewModel;

class Module implements AutoloaderProvider
{
    public static $config;
    protected static $layout;

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

        self::$layout = $layout   = new ViewModel();
        $layout->setTemplate('layout');
        $view->addResponseStrategy(function($e) use ($layout, $renderer) {
            $result = $e->getResult();
            $layout->setVariable('content', $result);
            $page   = $renderer->render($layout);
            $e->setResult($page);

            // Cleanup
            $headTitle = $renderer->plugin('headtitle');
            $headTitle->getContainer()->exchangeArray(array());
            $headTitle->append('phly, boy, phly');

            $headLink = $renderer->plugin('headLink');
            $headLink->getContainer()->exchangeArray(array());
            $headLink->__invoke(array(
                'rel' => 'shortcut icon',
                'type' => 'image/vnd.microsoft.icon',
                'href' => '/images/Application/favicon.ico',
            ));

            $headScript = $renderer->plugin('headScript');
            $headScript->getContainer()->exchangeArray(array());
        }, 100);
    }

    public static function handleTagCloud($cloud, $view, $config, $locator)
    {
        if (!self::$layout) {
            return;
        }

        self::$layout->setVariable('footer', sprintf(
            "<h4>Tag Cloud</h4>\n<div class=\"cloud\">\n%s</div>\n",
            $cloud->render()
        ));
    }
}
