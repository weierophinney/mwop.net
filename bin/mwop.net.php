#!/usr/bin/env php
<?php
namespace Mwop;

use Zend\Console\Console;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Http\Client as HttpClient;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
use ZF\Console\Application;

chdir(__DIR__ . '/../');
require_once 'vendor/autoload.php';

define('VERSION', '0.0.1');

$config   = include 'config/config.php';
$services = new ServiceManager(new Config($config['services']));
$services->setService('Config', $config);

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
            $handler = $services->get('Mwop\Github\Console\Fetch');
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
            $handler = $services->get('Mwop\Blog\Console\TagCloud');
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
            $handler = $services->get('Mwop\Blog\Console\FeedGenerator');
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'seed-blog-db',
        'route' => '[--path=] [--dbPath=]',
        'description' => 'Re-create the blog post database from the post entities.',
        'short_description' => 'Generate and seed the blog post database.',
        'options_descriptions' => [
            '--path'   => 'Base path of the application; posts are expected at $path/data/posts/',
            '--dbPath' => 'Path to the database file (defaults to data/posts.db)',
        ],
        'defaults' => [
            'path'   => realpath(getcwd()),
            'dbPath' => realpath(getcwd()) . '/data/posts.db',
        ],
        'handler' => function ($route, $console) use ($services) {
            $handler = $services->get('Mwop\Blog\Console\SeedBlogDatabase');
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'cache-posts',
        'route' => '[--path=]',
        'description' => 'Generate the static cache of all blog posts.',
        'short_description' => 'Cache blog posts.',
        'options_descriptions' => [
            '--path'   => 'Base path of the application; posts are expected at $path/data/posts/',
        ],
        'defaults' => [
            'path'   => realpath(getcwd()),
        ],
        'handler' => function ($route, $console) use ($services) {
            $handler = $services->get('Mwop\Blog\Console\CachePosts');
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'prep-page-cache-rules',
        'route' => '--appId= --site=',
        'description' => 'Prepare pagecache_rules.xml for deployment packaging.',
        'short_description' => 'Prep page cache rules',
        'options_descriptions' => [
            '--appId' => 'Zend Server application ID',
            '--site'  => 'Base URL of site to which to deploy',
        ],
        'handler' => function ($route, $console) use ($services) {
            $handler = $services->get('Mwop\Console\PrepPageCacheRules');
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
