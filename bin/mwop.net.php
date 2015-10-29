#!/usr/bin/env php
<?php
/**
 * @todo Rewrite code for generating blog DB to use Puli.
 * @todo Rewrite tag-cloud, cache-posts, feed-generator to depend on DB population;
 *     either have them call that task before running, or check for the DB, or look
 *     for a CLI flag that asks to update first.
 * @todo Maybe add a "blog-prepare" task that does all of the above at once, in order?
 */
namespace Mwop;

use Zend\Console\Console;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Http\Client as HttpClient;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
use ZF\Console\Application;

chdir(__DIR__ . '/../');
require_once 'vendor/autoload.php';

define('VERSION', '0.0.2');

$container = require 'config/container.php';

// Hack, to ensure routes are properly injected
$container->get('Zend\Expressive\Application');

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
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get('Mwop\Github\Console\Fetch');
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
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get('Mwop\Blog\Console\TagCloud');
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
            '--baseUri' => 'Base URI for the site (defaults to https://mwop.net)',
        ],
        'defaults' => [
            'outputDir' => 'data/feeds',
            'baseUri'   => 'https://mwop.net',
        ],
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get('Mwop\Blog\Console\FeedGenerator');
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'seed-blog-db',
        'route' => '[--path=] [--dbPath=] [--postsPath=] [--authorsPath=]',
        'description' => 'Re-create the blog post database from the post entities.',
        'short_description' => 'Generate and seed the blog post database.',
        'options_descriptions' => [
            '--path'        => 'Base path of the application; defaults to current working dir',
            '--dbPath'      => 'Path to the database file, relative to the --path; defaults to data/posts.db',
            '--postsPath'   => 'Path to the blog posts, relative to the --path; defaults to data/blog',
            '--authorsPath' => 'Path to the author metadata files, relative to the --path; defaults to data/blog/authors',
        ],
        'defaults' => [
            'path'        => realpath(getcwd()),
            'postsPath'   => 'data/blog/',
            'authorsPath' => 'data/blog/authors',
            'dbPath'      => 'data/posts.db',
        ],
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get('Mwop\Blog\Console\SeedBlogDatabase');
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'cache-posts',
        'route' => '[--path=]',
        'description' => 'Generate the static cache of all blog posts.',
        'short_description' => 'Cache blog posts.',
        'options_descriptions' => [
            '--path'   => 'Base path of the application; posts are expected at $path/data/blog/',
        ],
        'defaults' => [
            'path'   => realpath(getcwd()),
        ],
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get('Mwop\Blog\Console\CachePosts');
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
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get('Mwop\Console\PrepPageCacheRules');
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
