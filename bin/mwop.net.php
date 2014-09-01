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
        'route' => '[--output=] [--template=]',
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
    [
        'name' => 'tag-cloud',
        'route' => '[--output=]',
        'description' => 'Generate a Mustache template containing the tag cloud for the blog.',
        'short_description' => 'Generate tag cloud.',
        'options_descriptions' => [
            '--output'   => 'Output file to which to write the tag cloud',
        ],
        'defaults' => [
            'output' => 'data/tag-cloud.mustache',
        ],
        'handler' => function ($route, $console) use ($services) {
            $handler = $services->get('Mwop\Blog\TagCloud');
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'feed-generator',
        'route' => '[--outputDir=] [--baseUri=]',
        'description' => 'Generate feeds (RSS and Atom) for the blog, including all tags.',
        'short_description' => 'Generate blog feeds.',
        'options_descriptions' => [
            '--outputDir' => 'Directory to which to write the feeds (defaults to data/feeds)',
            '--baseUri' => 'Base URI for the blog (defaults to http://mwop.net/blog)',
        ],
        'defaults' => [
            'outputDir' => 'data/feeds',
            'baseUri'   => 'http://mwop.net/blog',
        ],
        'handler' => function ($route, $console) use ($services) {
            $handler = $services->get('Mwop\Blog\FeedGenerator');
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
