<?php

namespace Application;

use InvalidArgumentException,
    Zend\Config\Config,
    Zend\Di\Locator,
    Zend\Dojo\View\HelperLoader as DojoLoader,
    Zend\EventManager\EventCollection,
    Zend\EventManager\StaticEventCollection,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider;

class Module implements AutoloaderProvider
{
    protected $appListeners    = array();
    protected $staticListeners = array();
    protected $view;
    protected $viewListener;

    public function init()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'cacheRules'));
        $events->attach('bootstrap', 'bootstrap', array($this, 'initView'));
        $events->attach('bootstrap', 'bootstrap', array($this, 'registerApplicationListeners'), -10);
        $events->attach('bootstrap', 'bootstrap', array($this, 'registerStaticListeners'), -10);
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

    public function initView($e)
    {
        $app     = $e->getParam('application');
        $config  = $e->getParam('config');
        $locator = $app->getLocator();
        $router  = $app->getRouter();
        $view    = $locator->get('view');
        $url     = $view->plugin('url');
        $url->setRouter($router);

        $persistent = $view->placeholder('layout');
        foreach ($config->view as $var => $value) {
            if ($value instanceof Config) {
                $value = new Config($value->toArray(), true);
            }
            $persistent->{$var} = $value;
        }

        $view->doctype('HTML5');
        $view->getBroker()->getClassLoader()->registerPlugins(new DojoLoader());
        $view->headTitle()->setSeparator(' :: ')
                          ->setAutoEscape(false)
                          ->append('phly, boy, phly');
        $view->headLink(array(
            'rel'  => 'shortcut icon',
            'type' => 'image/vnd.microsoft.icon',
            'href' => '/images/Application/favicon.ico',
        ));
        $dojo = $view->plugin('dojo');
        $dojo->setCdnVersion('1.6')
             ->setDjConfig(array(
                 'isDebug'     => true,
                 'parseOnLoad' => true,
             ));
        $this->view = $view;
    }

    public function registerApplicationListeners($e)
    {
        $app          = $e->getParam('application');
        $config       = $e->getParam('config');
        $viewListener = $this->getViewListener($this->view, $config);
        $app->events()->attachAggregate($viewListener);
    }

    public function registerStaticListeners($e)
    {
        $locator      = $e->getParam('application')->getLocator();
        $config       = $e->getParam('config');
        $events       = StaticEventManager::getInstance();
        $viewListener = $this->getViewListener($this->view, $config);
        $viewListener->registerStaticListeners($events, $locator);
    }

    public function cacheRules($e)
    {
        $app      = $e->getParam('application');
        $locator  = $app->getLocator();
        $cacheListener = $locator->get('Cache\Listener');
        $cacheListener->addRule(function($e) {
            if (!$e instanceof \Zend\Mvc\MvcEvent) {
                return;
            }

            $routeMatch = $e->getRouteMatch();
            if (in_array($routeMatch->getMatchedRouteName(), array('default', 'comics'))) {
                // Do not cache 404 requests or the comics page
                return true;
            }
            return false;
        });
    }

    protected function getViewListener($view, $config)
    {
        if ($this->viewListener instanceof View\Listener) {
            return $this->viewListener;
        }

        $viewListener       = new View\Listener($view, $config);
        $viewListener->setDisplayExceptionsFlag($config->display_exceptions);

        $this->viewListener = $viewListener;
        return $viewListener;
    }

    public function getProvides()
    {
        return array(
            'name'    => 'Application',
            'version' => '0.1.0',
        );
    }

    public function getDependencies()
    {
        return array(
            'php' => array(
                'required' => true,
                'version'  => '>=5.3.1',
            ),
            'ext/mongo' => array(
                'required' => true,
                'version'  => '>=1.2.0',
            ),
            'Blog' => array(
                'required' => true,
                'version'  => '>=0.1.0',
            )
        );
    }
}
