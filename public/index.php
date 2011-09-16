<?php
ini_set("display_errors", true);
error_reporting(E_ALL | E_STRICT);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Define application path
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', (getenv('APPLICATION_PATH') ? getenv('APPLICATION_PATH') : realpath(__DIR__ . '/../')));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    '.',
    '/home/matthew/git/zf2/library',
)));

require_once 'Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\ClassMapAutoloader' => array(
        __DIR__ . '/../modules/Zf2Module/classmap.php',
    ),
    'Zend\Loader\StandardAutoloader' => array(),
));
// Init config
$appConfig = include __DIR__ . '/../configs/application.config.php';

/**
 * Long-hand:
 * $modules = new Zf2Module\ModuleManager;
 * $modules->getLoader()->registerPaths($appConfig->modulePaths->toArray());
 * $modules->loadModules($appConfig->modules->toArray());
 */
$modules = Zf2Module\ModuleManager::fromConfig($appConfig);

// Get the merged config object
$config = $modules->getMergedConfig();

// Create application, bootstrap, and run
$bootstrap = new $config->bootstrap_class($config);
$application = new Zf2Mvc\Application;
$bootstrap->bootstrap($application);
$application->run()->send();
