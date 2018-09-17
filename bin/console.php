#!/usr/bin/env php
<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

chdir(__DIR__ . '/../');
require_once 'vendor/autoload.php';

define('VERSION', '0.1.0');

$container = require 'config/container.php';

$loader = new ContainerCommandLoader($container, [
    'blog:clear-cache' => Blog\ClearCache::class,
]);

$application = new Application('mwop.net', VERSION);
$application->setCommandLoader(new ContainerCommandLoader($container, [
    'blog:clear-cache' => Blog\Console\ClearCache::class,
]));
$application->setDefaultCommand('list');
$application->run();
