<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;
use Mezzio\Hal\ConfigProvider;

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = [
    'config_cache_path' => 'data/cache/app_config.php',
];

$aggregator = new ConfigAggregator([
    ConfigProvider::class,
    Mezzio\Authorization\Rbac\ConfigProvider::class,
    Mezzio\Authorization\ConfigProvider::class,
    Mezzio\Authentication\Session\ConfigProvider::class,
    Mezzio\ProblemDetails\ConfigProvider::class,
    Laminas\Diactoros\ConfigProvider::class,
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

    // Conditional, as it will not be used in production
    class_exists(Mezzio\Tooling\ConfigProvider::class)
        ? Mezzio\Tooling\ConfigProvider::class
        : function (): array {
            return [];
        },

    // App-specific modules
    Mwop\App\ConfigProvider::class,
    Mwop\ActivityPub\ConfigProvider::class,
    Mwop\Art\ConfigProvider::class,
    Mwop\Blog\ConfigProvider::class,
    Mwop\Comics\ConfigProvider::class,
    Mwop\Console\ConfigProvider::class,
    Mwop\Contact\ConfigProvider::class,
    Mwop\Cron\ConfigProvider::class,
    Mwop\Feed\ConfigProvider::class,
    Mwop\Github\ConfigProvider::class,
    Mwop\Hooks\ConfigProvider::class,
    Mwop\Mastodon\ConfigProvider::class,
    Mwop\Now\ConfigProvider::class,

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
