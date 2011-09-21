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
    Zf2Mvc\Application;

class Bootstrap
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
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
        $router = $app->getLocator()->get('Zf2Mvc\Router\SimpleRouteStack');
        foreach ($this->config->routes as $name => $config) {
            $class   = $config->type;
            $options = $config->options;
            $route   = new $class($options);
            $router->addRoute($name, $route);
        }
        $app->setRouter($router);
    }

    protected function setupEvents(Application $app)
    {
        /**
         * Wire events into the Application's EventManager, and/or setup
         * static listeners for events that may be invoked.
         */
        $view = $this->getView($app);

        $layoutHandler = function($content, $response, $event = null) use ($view) {
            // Layout
            $vars = array('content' => $content);
            if ($event) {
                $footer = $event->getParam('footer');
                $vars['footer'] = $footer;
            }
            $vars       = new ViewVariables($vars);
            $layout     = $view->render('layout.phtml', $vars);

            $response->setContent($layout);
        };

        $events = StaticEventManager::getInstance();

        // Layout Rendering
        // If an event or caller sets a "content" parameter, we inject it into
        // the layout and return immediately.
        $events->attach('Zend\Stdlib\Dispatchable', 'dispatch', function($e) use ($view, $layoutHandler) {
            $response = $e->getParam('response');
            if ($response->getStatusCode() == 404 || $response->isRedirect() || $response->headers()->has('Location')) {
                return;
            } 

            $content = $e->getParam('content', false);
            if (!$content) {
                return;
            }

            $layoutHandler($content, $response, $e);
            return $response;
        }, -100);

        // View Rendering
        $events->attach('Application\Controller\PageController', 'dispatch', function($e) use ($view, $layoutHandler) {
            $page = $e->getResult();
            if ($page instanceof Response) {
                return;
            }

            $response = $e->getResponse();
            if ($response->getStatusCode() == 404) {
                return;
            } 

            $request    = $e->getRequest();
            $routeMatch = $e->getRouteMatch();
            if (!$routeMatch) {
                $page = '404';
            } else {
                $page = $routeMatch->getParam('page', '404');
            }

            $script     = 'pages/' . $page . '.phtml';

            // Action content
            $content    = $view->render($script);

            // Layout
            $layoutHandler($content, $response, $e);
            return $response;
        }, -50);

        $events->attach('Zend\Controller\ActionController', 'dispatch', function($e) use ($view, $layoutHandler) {
            $vars       = $e->getParam('__RESULT__');
            if ($vars instanceof Response) {
                return;
            }

            $response   = $e->getParam('response');
            if ($response->getStatusCode() == 404) {
                // Render 404 responses differently
                return;
            }

            $request    = $e->getParam('request');
            $routeMatch = $request->getMetadata('route-match');
            $controller = $routeMatch->getParam('controller', 'error');
            $action     = $routeMatch->getParam('action', 'index');
            $script     = $controller . '/' . $action . '.phtml';
            if (is_object($vars)) {
                if ($vars instanceof Traversable) {
                    $viewVars = new ViewVariables(array());
                    $vars = iterator_apply($vars, function($it) use ($viewVars) {
                        $viewVars[$it->key()] = $it->current();
                    }, $it);
                    $vars = $viewVars;
                } else {
                    $vars = new ViewVariables((array) $vars);
                }
            } else {
                $vars = new ViewVariables($vars);
            }

            // Action content
            $content    = $view->render($script, $vars);

            // Layout
            $layoutHandler($content, $response, $e);
            return $response;
        }, -50);

        // Render 404 pages
        $events->attach('Zend\Stdlib\Dispatchable', 'dispatch', function($e) use ($view, $layoutHandler) {
            $vars       = $e->getResult();
            if ($vars instanceof Response) {
                return;
            }

            $response   = $e->getResponse();
            if ($response->getStatusCode() != 404) {
                // Only handle 404's
                return;
            }

            $view->plugin('headTitle')->prepend('Page Not Found');
            $content = $view->render('page/404.phtml', $vars);

            // Layout
            $layoutHandler($content, $response, $e);
            return $response;
        }, -100);

        // Error handling
        $app->events()->attach('dispatch.error', function($e) use ($view, $layoutHandler) {
            $error   = $e->getParam('error');

            switch ($error) {
                case Application::ERROR_CONTROLLER_NOT_FOUND:
                    $script = 'page/404.phtml';
                    break;
                case Application::ERROR_CONTROLLER_INVALID:
                default:
                    $script = 'error.phtml';
                    break;
            }

            $view->plugin('headTitle')->prepend('Application Error');
            $content = $view->render($script);

            // Layout
            $response = $app->getResponse();
            $layoutHandler($content, $response, $e);
            return $response;
        });
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
        $view->plugin('headTitle')->setSeparator(' :: ')
                                  ->setAutoEscape(false)
                                  ->append('phly, boy, phly');
        $view->plugin('headLink')->appendStylesheet('/css/Application/reset.css')
                                 ->appendStylesheet('/css/Application/text.css')
                                 ->appendStylesheet('/css/Application/960.css')
                                 ->appendStylesheet('/css/Application/site.css')
                                 ->direct(array(
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
