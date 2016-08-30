#!/usr/bin/env php
<?php
/**
 * @todo Rewrite code for generating blog DB to use Puli.
 * @todo Rewrite tag-cloud, cache-posts, feed-generator to depend on DB population;
 *     either have them call that task before running, or check for the DB, or look
 *     for a CLI flag that asks to update first.
 * @todo Maybe add a "blog-prepare" task that does all of the above at once, in order?
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */
namespace Mwop;

use Mwop\Console as MwopConsole;
use Zend\Console\Console;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Http\Client as HttpClient;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
use ZF\Console\Application;

chdir(__DIR__ . '/../');
require_once 'vendor/autoload.php';

define('VERSION', '0.0.5');

$container = require 'config/container.php';

// Hack, to ensure routes are properly injected
$container->get('Zend\Expressive\Application');

$routes = [
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
            $handler = $container->get(Blog\Console\CachePosts::class);
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'clear-cache',
        'route' => '',
        'description' => 'Clear any cached content.',
        'short_description' => 'Clear the static cache.',
        'defaults' => [
            'path' => realpath(getcwd()),
        ],
        'handler' => function ($route, $console) {
            $handler = new MwopConsole\ClearCache();
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'create-asset-symlinks',
        'description' => 'Symlink assets installed by npm into the public tree.',
        'short_description' => 'Symlink assets.',
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get(MwopConsole\CreateAssetSymlinks::class);
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
            $handler = $container->get(Blog\Console\FeedGenerator::class);
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'generate-search-data',
        'route' => '[--path=]',
        'description' => 'Generate site search data based on blog posts.',
        'short_description' => 'Generate site search data.',
        'options_descriptions' => [
            '--path'   => 'Base path of the application; posts are expected at $path/data/blog/ '
            . 'and search terms will be written to $path/data/search_terms.json',
        ],
        'defaults' => [
            'path'   => realpath(getcwd()),
        ],
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get(Blog\Console\GenerateSearchData::class);
            return $handler($route, $console);
        },
    ],
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
            $handler = $container->get(Github\Console\Fetch::class);
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'homepage-feeds',
        'route' => '',
        'description' => 'Fetch feed data for homepage activity stream.',
        'short_description' => 'Fetch homepage feed data.',
        'defaults' => [
            'path' => realpath(getcwd()),
        ],
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get(MwopConsole\FeedAggregator::class);
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'prep-offline-pages',
        'route' => '[--serviceWorker=]',
        'description' => 'Prepare the offline pages list for the service-worker.js file.',
        'short_description' => 'Prep offline page cache list',
        'options_descriptions' => [
            '--serviceWorker' => 'Path to the service-worker.js file',
        ],
        'defaults' => [
            'serviceWorker'   => realpath(getcwd()) . '/public/service-worker.js',
        ],
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get(MwopConsole\PrepOfflinePages::class);
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
            '--authorsPath' => 'Path to the author metadata files, relative to the --path; '
            . 'defaults to data/blog/authors',
        ],
        'defaults' => [
            'path'        => realpath(getcwd()),
            'postsPath'   => 'data/blog/',
            'authorsPath' => 'data/blog/authors',
            'dbPath'      => 'data/posts.db',
        ],
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get(Blog\Console\SeedBlogDatabase::class);
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
            $handler = $container->get(Blog\Console\TagCloud::class);
            return $handler($route, $console);
        },
    ],
    [
        'name' => 'use-dist-templates',
        'route' => '[--path=]',
        'description' => 'Enable usage of distribution templates (optimizing CSS and JS).',
        'short_description' => 'Use dist templates.',
        'options_descriptions' => [
            '--path'   => 'Base path of the application; templates are expected at $path/templates/',
        ],
        'defaults' => [
            'path'   => realpath(getcwd()),
        ],
        'handler' => function ($route, $console) use ($container) {
            $handler = $container->get(MwopConsole\UseDistTemplates::class);
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
