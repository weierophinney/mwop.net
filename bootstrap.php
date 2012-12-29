<?php
// Switch to this directory
chdir(__DIR__);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Setup error reporting
ini_set("display_errors", (('production' == APPLICATION_ENV) ? false : true));
error_reporting(E_ALL | E_STRICT);

date_default_timezone_set('America/Chicago');

// Setup autoloading
include_once 'vendor/autoload.php';

// Bootstrap application
$application = Zend\Mvc\Application::init(include 'config/application.config.php');
return $application;
