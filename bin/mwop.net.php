#!/usr/bin/env php
<?php
namespace Mwop;

use Zend\Console\Console;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Http\Client as HttpClient;
use ZF\Console\Application;

chdir(__DIR__ . '/../');
require_once 'vendor/autoload.php';

define('VERSION', '0.0.1');

$config = include 'config/config.php';

$http   = new HttpClient();
$http->setOptions(array(
    'adapter' => 'Zend\Http\Client\Adapter\Curl',
));
FeedReader::setHttpClient($http);

$reader = new Github\AtomReader($config['github']['user'], $config['github']['token']);
$reader->setLimit($config['github']['limit']);

$fetch = new Github\Fetch($reader);

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
        'handler' => $fetch,
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
