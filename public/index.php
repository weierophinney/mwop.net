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

$app->pipe($services->get('Mwop\QueryParams'));
$app->pipe($services->get('Mwop\Redirects'));
$app->pipe($services->get('Mwop\BodyParams'));

// Everything else... is templated.
$app->pipe(function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\Templated');

    // Home page
    $middleware->pipe('/', $services->get('Mwop\HomePage'));

    // Blog
    $middleware->pipe('/blog', function ($req, $res, $next) use ($services) {
        $blog = $services->get('Mwop\Blog\Middleware');
        $blog($req, $res, $next);
    });

    // Contact form
    $middleware->pipe('/contact', function ($req, $res, $next) use ($services) {
        $contact = $services->get('Mwop\Contact\Middleware');
        $contact($req, $res, $next);
    });

    // Comics
    $middleware->pipe('/comics', function ($req, $res, $next) use ($services) {
        $comics = new Middleware();
        $comics->pipe($services->get('Mwop\Auth\UserSession'));
        $comics->pipe($services->get('Mwop\ComicsPage'));
        $comics($req, $res, $next);
    });

    // Resume
    $middleware->pipe('/resume', $services->get('Mwop\ResumePage'));

    $middleware($req, $res, $next);
});

// Authentication (opauth)
$app->pipe('/auth', function ($req, $res, $next) use ($services) {
    $middleware = $services->get('Mwop\Auth\Middleware');
    $middleware($req, $res, $next);
});

// Job Queue jobs
$app->pipe('/jobs', function ($req, $res, $next) {
    $middleware = new Job\Middleware();
    $middleware($req, $res, $next);
});

// Errors
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

// Start listening
$server = Server::createServer($app, $_SERVER);
$server->listen();
