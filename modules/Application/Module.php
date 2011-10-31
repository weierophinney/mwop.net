<?php

namespace Application;

use InvalidArgumentException,
    Zend\Config\Config,
    Zend\Di\Locator,
    Zend\Dojo\View\HelperLoader as DojoLoader,
    Zend\Loader\AutoloaderFactory,
    Zend\EventManager\EventCollection,
    Zend\EventManager\StaticEventCollection,
    Zend\EventManager\StaticEventManager;

class Module
{
    protected $appListeners    = array();
    protected $staticListeners = array();
    protected $view;
    protected $viewListener;

    public function init()
    {
        $this->initAutoloader();
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'initView'));
        $events->attach('bootstrap', 'bootstrap', array($this, 'registerApplicationListeners'), -10);
        $events->attach('bootstrap', 'bootstrap', array($this, 'registerStaticListeners'), -10);
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

    public function initView($e)
    {
        $app     = $e->getParam('application');
        $config  = $e->getParam('modules')->getMergedConfig();
        $locator = $app->getLocator();
        $router  = $app->getRouter();
        $view    = $locator->get('view');
        $url     = $view->plugin('url');
        $url->setRouter($router);

        if ($config->disqus) {
            // Ensure disqus plugin is configured
            $disqus = $view->plugin('disqus', $config->disqus->toArray());
        }

        $persistent = $view->placeholder('layout');
        foreach ($config->view as $var => $value) {
            $persistent->{$var} = $value;
        }

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
        $config       = $e->getParam('modules')->getMergedConfig();
        $viewListener = $this->getViewListener($this->view, $config);
        $app->events()->attachAggregate($viewListener);
    }

    public function registerStaticListeners($e)
    {
        $locator      = $e->getParam('application')->getLocator();
        $config       = $e->getParam('modules')->getMergedConfig();
        $events       = StaticEventManager::getInstance();
        $viewListener = $this->getViewListener($this->view, $config);
        $viewListener->registerStaticListeners($events, $locator);
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
