<?php

namespace Application;

use Traversable;
use Zend\Config\Config;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\View\Http\ViewManager;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model;
use Zend\View\Renderer\PhpRenderer;

class Module
{
    protected static $layout;
    protected $config;

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
                array(
                    'Hybrid_Storage'               => getcwd() . '/vendor/hybridauth/hybridauth/hybridauth/Hybrid/Storage.php',
                    'Hybrid_Error'                 => getcwd() . '/vendor/hybridauth/hybridauth/hybridauth/Hybrid/Error.php',
                    'Hybrid_Logger'                => getcwd() . '/vendor/hybridauth/hybridauth/hybridauth/Hybrid/Logger.php',
                    'Hybrid_Auth'                  => getcwd() . '/vendor/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php',
                    'Hybrid_Provider_Model'        => getcwd() . '/vendor/hybridauth/hybridauth/hybridauth/Hybrid/Provider_Model.php',
                    'Hybrid_Provider_Model_OAuth2' => getcwd() . '/vendor/hybridauth/hybridauth/hybridauth/Hybrid/Provider_Model_OAuth2.php',
                    'Hybrid_Providers_GitHub'      => getcwd() . '/vendor/hybridauth/hybridauth/additional-providers/hybridauth-github/Providers/GitHub.php',
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array('initializers' => array(
            array($this, 'initViewVariables'),
        ));
    }

    public function getViewHelperConfig()
    {
        return array('factories' => array(
            'disqus' => function ($helpers) {
                $services = $helpers->getServiceLocator();
                $config   = $services->get('config');
                $config   = $config['disqus'];
                return new View\Helper\Disqus($config);
            },
        ));
    }

    public function onBootstrap($e)
    {
        $app          = $e->getApplication();
        $services     = $app->getServiceManager();
        $events       = $app->getEventManager();
        $this->config = $services->get('config');
        $services->setService('MvcEvent', $e);
        $this->initAcls($e);
        $this->initView($e);

        $moduleRouteListener = new ModuleRouteListener();
        $events->attach($moduleRouteListener);
        $events->getSharedManager()->attach('PhlySimplePage\PageController', 'dispatch', array($this, 'onPageDispatchPost'), -20);
    }

    public function initAcls($e)
    {
        $app = $e->getParam('application');
        $app->getEventManager()->attach('route', array($this, 'checkAcls'), -80);
    }

    public function initView($e)
    {
        $app      = $e->getApplication();
        $services = $app->getServiceManager();
        if (!$services->has('ViewRenderer')) {
            return;
        }
        $config   = $this->config;
        $view     = $services->get('ViewRenderer');
        $this->initViewVariables($view);

        $view->headTitle()->setSeparator(' :: ')
                          ->setAutoEscape(false)
                          ->append('phly, boy, phly');
        $view->headLink(array(
            'rel'  => 'shortcut icon',
            'type' => 'image/vnd.microsoft.icon',
            'href' => '/images/Application/favicon.ico',
        ));
    }

    public function initViewVariables($renderer, $services = null)
    {
        if (!$renderer instanceof PhpRenderer) {
            return;
        }

        if (!isset($this->config) && null === $services) {
            return;
        }

        if (!isset($this->config)) {
            $this->config = $services->get('Config');
        }

        $config = $this->config;
        if (!isset($config['view'])) {
            return;
        }

        $config     = $config['view'];
        $persistent = $renderer->placeholder('layout');
        foreach ($config as $var => $value) {
            if (is_array($value)) {
                $value = new Config($value, true);
            }
            $persistent->{$var} = $value;
        }
    }

    public static function prepareCompilerView($view, $config, $services)
    {
        $renderer  = $services->get('BlogRenderer');
        $view->addRenderingStrategy(function($e) use ($renderer) {
            return $renderer;
        }, 100);

        self::$layout = $layout   = new Model\ViewModel();
        $layout->setTemplate('layout');
        $view->addResponseStrategy(function($e) use ($layout, $renderer) {
            $result = $e->getResult();
            $layout->setVariable('content', $result);
            $page   = $renderer->render($layout);
            $e->setResult($page);

            // Cleanup
            $headTitle = $renderer->plugin('headtitle');
            $headTitle->getContainer()->exchangeArray(array());
            $headTitle->setAutoEscape(false)
                      ->setSeparator(' :: ')
                      ->append('phly, boy, phly');

            $headLink = $renderer->plugin('headLink');
            $headLink->getContainer()->exchangeArray(array());
            $headLink(array(
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

    public function checkAcls($e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            return;
        }

        $controller = $routeMatch->getParam('controller', false);
        if ($controller != 'PhlySimplePage\Controller\Page') {
            return;
        }

        $protectedActions = $this->config['authentication']['PhlySimplePage\Controller\PageController'];
        $page             = $routeMatch->getParam('template');
        if (!in_array($page, $protectedActions)) {
            // does not require authorization
            return;
        }

        $app     = $e->getApplication();
        $locator = $app->getServiceManager();
        $auth    = $locator->get('zfcuser_auth_service');
        if ($auth->hasIdentity()) {
            // authorized
            return;
        }
        $baseModel = $e->getViewModel();
        return $this->createUnauthorizedResponse($app, $baseModel);
    }

    public function createUnauthorizedResponse($app, $baseModel)
    {
        $request  = $app->getRequest();
        $response = $app->getResponse();
        $locator  = $app->getServiceManager();
        $view     = $locator->get('View');
        $helpers  = $locator->get('ViewHelperManager');
        $url      = $helpers->get('url');
        $origin   = $url();

        $view->setRequest($request);
        $view->setResponse($response);

        $model = new Model\ViewModel();
        $model->setTemplate('pages/401');
        $model->setVariable('redirect', $origin);

        if ($baseModel instanceof Model\ModelInterface) {
            $baseModel->addChild($model);
            $model = $baseModel;
        }

        $view->render($model);
        return $response;
    }

    public function onPageDispatchPost($e)
    {
        $result = $e->getResult();
        if (!$result instanceof Model\ModelInterface) {
            return;
        }
        $template = $result->getTemplate();
        if (empty($template)) {
            return;
        }

        $app      = $e->getApplication();
        $services = $app->getServiceManager();
        $config   = $services->get('Config');
        if (!isset($config['xhr']) || !is_array($config['xhr'])) {
            return;
        }
        if (!in_array($template, $config['xhr'])) {
            return;
        }

        $result->setTerminal(true);
    }
}
