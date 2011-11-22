<?php
ini_set("display_errors", true);
error_reporting(E_ALL | E_STRICT);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Define application path
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', (getenv('APPLICATION_PATH') ? getenv('APPLICATION_PATH') : realpath(__DIR__)));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    '.',
    __DIR__ . '/library',
    get_include_path(),
)));

require_once 'Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array('fallback_autoloader' => false),
));

$appConfig = include __DIR__ . '/config/application.config.php';

$moduleLoader = new Zend\Loader\ModuleAutoloader($appConfig['module_paths']);
$moduleLoader->register();

$moduleManager   = new Zend\Module\Manager($appConfig['modules']);
$listenerOptions = new Zend\Module\Listener\ListenerOptions($appConfig['module_listener_options']);
$moduleManager->setDefaultListenerOptions($listenerOptions);
$moduleManager->getConfigListener()->addConfigGlobPath(__DIR__ . '/config/autoload/*.config.php');
$moduleManager->loadModules();

// Create application, bootstrap, and run
$bootstrap   = new Zend\Mvc\Bootstrap($moduleManager->getMergedConfig());
$application = new Zend\Mvc\Application;
$bootstrap->bootstrap($application);

/*
echo "<pre>";
Zend\Di\Display\Console::export($application->getLocator(), array(
    'Blog\Controller\EntryController',
    'Zend\Mail\Mail',
));
echo "</pre>";
/*
$obj = $application->getLocator()->get('Blog\Controller\EntryController');
echo "<pre>";
echo var_export($obj, 1);
echo "</pre>";
exit();
 */
