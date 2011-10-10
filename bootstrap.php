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
    '/home/matthew/git/zf2/library',
    get_include_path(),
)));

require_once 'Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array('fallback_autoloader' => false),
));

$appConfig = include __DIR__ . '/configs/application.config.php';

$moduleLoader = new Zend\Loader\ModuleAutoloader($appConfig->module_paths);
$moduleLoader->register();

$moduleManager = new Zend\Module\Manager(
    $appConfig->modules,
    new Zend\Module\ManagerOptions($appConfig->module_config)
);

// Get the merged config object
$config = $moduleManager->getMergedConfig();

// Create application, bootstrap, and run
$bootstrap = new $config->bootstrap_class($config, $moduleManager);
$application = new Zend\Mvc\Application;
$bootstrap->bootstrap($application);
