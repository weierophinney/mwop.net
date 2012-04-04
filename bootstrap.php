<?php
use Zend\Loader\AutoloaderFactory,
    Zend\Module\Listener,
    Zend\Module\Manager as ModuleManager,
    Zend\Mvc\Application,
    Zend\Mvc\Bootstrap;

// Switch to this directory
chdir(__DIR__);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Setup error reporting
ini_set("display_errors", (('production' == APPLICATION_ENV) ? false : true));
error_reporting(E_ALL | E_STRICT);

// Setup autoloading
require_once __DIR__ . '/vendor/ZendFramework/library/Zend/Loader/AutoloaderFactory.php';
AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(),
));

// Get application configuration
$appConfig = include __DIR__ . '/config/application.config.php';

// Setup and configure module listeners
$listenerOptions = new Listener\ListenerOptions($appConfig['module_listener_options']);
$listeners       = new Listener\DefaultListenerAggregate($listenerOptions);
$configListener  = $listeners->getConfigListener();
$configListener->addConfigGlobPath(
    __DIR__ . '/config/autoload/*.config.{global,local}.php'
);

// Setup and configure module manager, and load modules
$moduleManager   = new ModuleManager($appConfig['modules']);
$moduleManager->events()->attachAggregate($listeners);
$moduleManager->loadModules();

// Create application, bootstrap, and run
$bootstrap   = new Bootstrap($configListener->getMergedConfig());
$application = new Application;
$bootstrap->bootstrap($application);

//echo "<pre>";
//Zend\Di\Display\Console::export($application->getLocator(), array(
    //'Application\Controller\PageController',
    //// 'Zend\Mail\Mail',
//));
//echo "</pre>";
//$obj = $application->getLocator()->get('Blog\Controller\EntryController');
//echo "<pre>";
//echo var_export($obj, 1);
//echo "</pre>";
//exit();
