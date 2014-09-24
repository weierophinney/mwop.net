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

$config = include 'config/config.php';

$services = createServiceContainer($config);

$app = new Middleware();

$app->pipe(function ($req, $res, $next) {
    $req->view = (object) [
        'template' => null,
        'model'    => [],
    ];
    $next();
});
$app->pipe($services->get('Mwop\QueryParams'));
$app->pipe($services->get('Mwop\Redirects'));
$app->pipe($services->get('Mwop\BodyParams'));

$app->pipe('/', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\HomePage');
    $middleware($req, $res, $next);
});

$app->pipe('/comics', function ($req, $res, $next) use ($services) {
    $middleware   = new Middleware();
    $middleware->pipe($services->get('Mwop\Auth\UserSession'));
    $middleware->pipe($services->get('Mwop\ComicsPage'));
    $middleware($req, $res, $next);
});

$app->pipe('/resume', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\ResumePage');
    $middleware($req, $res, $next);
});

$app->pipe('/contact', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\Contact\Middleware');
    $middleware($req, $res, $next);
});

$app->pipe('/auth', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\Auth\Middleware');
    $middleware($req, $res, $next);
});

$app->pipe('/blog', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\Blog\Middleware');
    $middleware($req, $res, $next);
});

$app->pipe('/jobs', function ($req, $res, $next) {
    $middleware = new Job\Middleware();
    $middleware($req, $res, $next);
});

$app->pipe(function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\View');
    $middleware($req, $res, $next);
});

$app->pipe(new NotFound());

$app->pipe(function ($err, $req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\Unauthorized');
    $middleware($err, $req, $res, $next);
});
$app->pipe(function ($err, $req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\NotAllowed');
    $middleware($err, $req, $res, $next);
});
$app->pipe(function ($err, $req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\ErrorHandler');
    $middleware($err, $req, $res, $next);
});

$server = Server::createServer($app, $_SERVER);
$server->listen();
