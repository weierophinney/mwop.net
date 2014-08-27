<?php
use Mwop\NotAllowed;
use Mwop\QueryParams;
use Mwop\Unauthorized;
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

$config = include 'config.php';

$services = Mwop\createServiceContainer($config['services']);

$app = new Middleware();

$app->pipe($services->get('query-params'));

$app->pipe('/', $services->get('page.home'));
$app->pipe('/resume', $services->get('page.resume'));

$app->pipe('/contact', function ($req, $res, $next) {
    if (! in_array($req->getMethod(), ['GET', 'POST'])) {
        $res->setStatusCode(405);
        return $next(['GET', 'POST']);
    }
    $res->end('CONTACT!');
});

$app->pipe($services->get('not-allowed'));

$server = Server::createServer($app, $_SERVER);
$server->listen();
