#!/usr/bin/env php
<?php
namespace Mwop;

use Zend\Console\Console;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Http\Client as HttpClient;
use ZF\Console\Application;

chdir(__DIR__ . '/../');
require_once 'vendor/autoload.php';
require_once 'src/functions.php';

define('VERSION', '0.0.1');

$config   = include 'config/config.php';
$services = createServiceContainer($config);

$routes = [
    [
        'name' => 'github-links',
        'route' => '--output= [--template=]',
        'description' => 'Fetch GitHub activity stream and generate links for the home page.',
        'short_description' => 'Fetch GitHub activity stream.',
        'options_descriptions' => [
            '--output'   => 'Output file to which to write links',
            '--template' => 'Template string to use when generating link output',
        ],
        'defaults' => [
            'output' => 'data/github-links.mustache',
        ],
        'handler' => function ($route, $console) use ($services) {
            $handler = $services->get('Mwop\Github\Fetch');
            return $handler($route, $console);
        },
    ],
];

$app = new Application(
    'mwop.net',
    VERSION,
    $routes,
    Console::getInstance()
);
$exit = $app->run();
exit($exit);
