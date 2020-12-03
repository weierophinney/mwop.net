---
id: 2020-12-04-mezzio-development-mode-modules
author: matthew
title: 'Development-Mode Modules for Mezzio'
draft: false
public: true
created: '2020-12-04T11:45:00-05:00'
updated: '2020-12-04T11:45:00-05:00'
tags:
    - php
    - laminas
    - mezzio
---

I fielded a question in the [Laminas Slack](https://laminas.dev/chat) yesterday that I realized should likely be a blog post.
The question was:

> Is there a way to register development-mode-only modules in Mezzio?

There's actually multiple ways to do it, though one that is probably more preferable to others.

<!--- EXTENDED -->

### Conditional ConfigProviders

We already provide one pattern for doing this in the [Mezzio skeleton application](https://github.com/mezzio/mezzio-skeleton), by conditionally including the [mezzio-swoole](https://docs.mezzio.dev/mezzio-swoole/) `ConfigProvider` if the class is present:

```php
class_exists(\Some\ConfigProvider::class)
    ? \Some\ConfigProvider::class
    : function (): array { return []; },
```

Alternately, you could express this as an anonymous function:

```php
function (): array {
    if (class_exists(\Some\ConfigProvider::class)) {
        return (new \Some\ConfigProvider())();
    }
    return [];
},
```

(The values provided to the `ConfigAggregator` constructor can be either string class names of config providers, or functions returning arrays, which is why either of these will work.)

This approach is primarily useful if the config provider will only be installed as a `require-dev` dependency.
But what if you are defining the config provider in your own code, and it's **always** present?

### Development-Mode Configuration Aggregation

Another possibility is to do some "hacking" around how [laminas-development-mode](https://github.com/laminas/laminas-development-mode) works with Mezzio.
laminas-development-mode in Mezzio works with the `config/autoload/development.local.php.dist` file; enabling development mode symlinks config/autoload/development.local.php to that file.
That file just needs to return an array.
As such, you could totally write it to aggregate other config providers, as well as some default development configuration, using the same tools you do in your primary configuration file:

```php
// in config/autoload/development.local.php.dist:

declare(strict_types=1);

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;

$developmentConfig = [
    // app-level development config you want to define
];

$aggregator = new ConfigAggregator([
    // any ConfigProviders you want to list, then:
    new ArrayProvider($developmentConfig),
]);

return $aggregator->getMergedConfig();
```

This approach is likely the best to use, as it makes it more clear in your main config what the default modules are, and any dev-only ones are now listed in this file.
