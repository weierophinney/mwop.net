<?php
namespace Mwop;

<<<<<<< HEAD

ini_set('display_errors', 1);
error_reporting(E_ALL);
=======
use Zend\Expressive\Application;
>>>>>>> origin/master

// Delegate static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';

<<<<<<< HEAD
$container = require 'config/services.php';
$app = $container->get('Mwop\Site');
=======
$container = require 'config/container.php';
$app       = $container->get(Application::class);
>>>>>>> origin/master
$app->run();
