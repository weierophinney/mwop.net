<?php
namespace Application;

use Zend\Config\Config,
    Zend\Di\Configuration,
    Zend\Di\Definition,
    Zend\Di\Definition\Builder,
    Zend\Di\DependencyInjector,
    Zend\Dojo\View\HelperLoader as DojoLoader,
    Zend\EventManager\StaticEventManager,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\View\Variables as ViewVariables,
    Zend\Module\Manager as ModuleManager,
    Zend\Mvc\Application;

class Bootstrap
{
    protected $config;
    protected $modules;

    public function __construct(Config $config, ModuleManager $modules)
    {
        $this->config  = $config;
        $this->modules = $modules; 
    }

    public function bootstrap(Application $app)
    {
        $this->setupLocator($app);
        $this->setupRoutes($app);
        $this->setupEvents($app);
    }

    protected function setupLocator(Application $app)
    {
        /**
         * Instantiate and configure a DependencyInjector instance, or 
         * a ServiceLocator, and return it.
         */
        $definition = new Definition\AggregateDefinition;
        $definition->addDefinition(new Definition\RuntimeDefinition);

        $di = new DependencyInjector;
        $di->setDefinition($definition);

        $config = new Configuration($this->config->di);
        $config->configure($di);

        $app->setLocator($di);
    }

    protected function setupRoutes(Application $app)
    {
        /**
         * Pull the routing table from configuration, and pass it to the
         * router composed in the Application instance.
         */
        $router = $app->getLocator()->get('Zend\Mvc\Router\SimpleRouteStack');
        foreach ($this->config->routes as $name => $config) {
            $class   = $config->type;
            $options = $config->options;
            $route   = new $class($options);
            $router->addRoute($name, $route);
        }
        $app->setRouter($router);
    }

    /**
     * Wire events into the Application's EventManager, and/or setup
     * static listeners for events that may be invoked.
     */
    protected function setupEvents(Application $app)
    {
        $view         = $this->getView($app);
        $locator      = $app->getLocator();
        $events       = $app->events();
        $staticEvents = StaticEventManager::getInstance();

        foreach ($this->modules->getLoadedModules() as $name => $module) {
            if (method_exists($module, 'registerApplicationListeners')) {
                $module->registerApplicationListeners($events, $locator, $this->config);
            }

            if (method_exists($module, 'registerStaticListeners')) {
                $module->registerStaticListeners($staticEvents, $locator, $this->config);
            }
        }
    }

    protected function getView($app)
    {
        $di     = $app->getLocator();
        $view   = $di->get('view');
        $url    = $view->plugin('url');
        $url->setRouter($app->getRouter());

        if ($this->config->disqus) {
            // Ensure disqus plugin is configured
            $disqus = $view->plugin('disqus', $this->config->disqus->toArray());
        }

        $view->getBroker()->getClassLoader()->registerPlugins(new DojoLoader());
        $view->headTitle()->setSeparator(' :: ')
                          ->setAutoEscape(false)
                          ->append('phly, boy, phly');
        $view->headLink()->appendStylesheet('/css/Application/reset.css')
                         ->appendStylesheet('/css/Application/text.css')
                         ->appendStylesheet('/css/Application/960.css')
                         ->appendStylesheet('/css/Application/site.css');
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

        return $view;
    }
}
