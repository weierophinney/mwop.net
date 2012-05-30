<?php
use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfiguration;
use Zend\ServiceManager\ServiceManager;

// Switch to this directory
chdir(__DIR__);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Setup error reporting
ini_set("display_errors", (('production' == APPLICATION_ENV) ? false : true));
error_reporting(E_ALL | E_STRICT);

// Setup autoloading
include_once 'vendor/autoload.php';

// Get application stack configuration
$appConfig = include __DIR__ . '/config/application.config.php';

// Setup service manager
$services = new ServiceManager(new ServiceManagerConfiguration($appConfig['service_manager']));
$services->setService('ApplicationConfiguration', $appConfig);
$services->get('ModuleManager')->loadModules();

// Bootstrap application
$application = $services->get('Application');
$application->bootstrap();

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
