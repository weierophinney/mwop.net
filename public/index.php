<?php
namespace Mwop;

use Phly\Conduit\Middleware;
use Phly\Http\Server;

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(__DIR__ . '/../');
require_once 'vendor/autoload.php';
require_once 'src/functions.php';

$config = include 'config/config.php';

$services = createServiceContainer($config);

$app = new Middleware();

$app->pipe($services->get('query-params'));
$app->pipe($services->get('body-params'));

$app->pipe('/', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('page.home');
    $middleware($req, $res, $next);
});
$app->pipe('/comics', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\ComicsPage');
    $middleware($req, $res, $next);
});
$app->pipe('/resume', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('page.resume');
    $middleware($req, $res, $next);
});

$app->pipe('/contact', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('contact');
    $middleware($req, $res, $next);
});

$app->pipe('/auth', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\User\Middleware');
    $middleware($req, $res, $next);
});

$app->pipe('/blog', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\Blog\Middleware');
    $middleware($req, $res, $next);
});

$app->pipe(function ($err, $req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\Unauthorized');
    $middleware($err, $req, $res, $next);
});
$app->pipe(function ($err, $req, $res, $next) use ($services) {
    $middleware = $services->get('not-allowed');
    $middleware($err, $req, $res, $next);
});

$server = Server::createServer($app, $_SERVER);
$server->listen();
