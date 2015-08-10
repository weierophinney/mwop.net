<?php
namespace Mwop;

use Zend\Stratigility\MiddlewarePipe;
use Zend\Diactoros\Server;

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(__DIR__ . '/../');
require_once 'vendor/autoload.php';

$config = include 'config/config.php';

$services = createServiceContainer($config);

$app = new MiddlewarePipe();

// Basic functionality required everywhere
$app->pipe($services->get('Mwop\Redirects'));
$app->pipe($services->get('Mwop\BodyParams'));

// Site services
$app->pipe($services->get('Mwop\Site'));

// Errors
$app->pipe(new NotFound());
$app->pipe(function ($err, $req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\Unauthorized');
    return $middleware($err, $req, $res, $next);
});
$app->pipe(function ($err, $req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\NotAllowed');
    return $middleware($err, $req, $res, $next);
});
$app->pipe(function ($err, $req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\ErrorHandler');
    return $middleware($err, $req, $res, $next);
});

// Start listening
$server = Server::createServer($app, $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$server->listen();
