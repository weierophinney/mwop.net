<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

use Dotenv\Dotenv;
use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$dotEnvFile = dirname(__DIR__) . '/.env';
if (file_exists($dotEnvFile)) {
    Dotenv::createImmutable(dirname(__DIR__))->load();
}

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = [
    'config_cache_path' => 'data/cache/app_config.php',
];

$aggregator = new ConfigAggregator([
    \Laminas\Diactoros\ConfigProvider::class,
    Phly\Swoole\TaskWorker\ConfigProvider::class,
    Phly\EventDispatcher\ConfigProvider::class,
    Mezzio\Authentication\ConfigProvider::class,
    Mezzio\Session\Cache\ConfigProvider::class,
    Mezzio\Plates\ConfigProvider::class,
    Laminas\Paginator\ConfigProvider::class,
    Laminas\InputFilter\ConfigProvider::class,
    Laminas\Filter\ConfigProvider::class,
    Laminas\Validator\ConfigProvider::class,
    Mezzio\Session\ConfigProvider::class,
    Mezzio\Csrf\ConfigProvider::class,
    Mezzio\ConfigProvider::class,
    Laminas\HttpHandlerRunner\ConfigProvider::class,
    Mezzio\Helper\ConfigProvider::class,
    Mezzio\Router\ConfigProvider::class,
    Mezzio\Router\FastRouteRouter\ConfigProvider::class,
    Mezzio\Swoole\ConfigProvider::class,

    // App-specific modules
    Mwop\App\ConfigProvider::class,
    Mwop\Blog\ConfigProvider::class,
    Mwop\Console\ConfigProvider::class,
    Mwop\Contact\ConfigProvider::class,
    Mwop\Github\ConfigProvider::class,
    Mwop\OAuth2\ConfigProvider::class,

    // Include cache configuration
    new ArrayProvider($cacheConfig),

    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `global.php`
    //   - `*.global.php`
    //   - `local.php`
    //   - `*.local.php`
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),

    // Load development config if it exists
    new PhpFileProvider('config/development.config.php'),
], $cacheConfig['config_cache_path']);

return $aggregator->getMergedConfig();
