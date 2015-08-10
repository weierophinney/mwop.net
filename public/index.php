<?php
namespace Mwop;

use Zend\Diactoros\Server;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

// Delegate static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(__DIR__ . '/../');
require_once 'vendor/autoload.php';

$config   = include 'config/config.php';
$services = new ServiceManager(new Config($config['services']));
$services->setService('Config', $config);

$app = $services->get('Mwop\Site');
$app->run();
