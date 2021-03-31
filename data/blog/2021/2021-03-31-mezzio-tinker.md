---
id: 2021-03-31-mezzio-tinker
author: matthew
title: 'Tinker-like REPL for Mezzio'
draft: false
public: true
created: '2021-03-31T09:50:00-05:00'
updated: '2021-03-31T09:50:00-05:00'
tags:
    - php
    - laminas
    - mezzio
---

Today in the [Laminas Slack](https://laminas.dev/chat), somebody asked if there was an equivalent to Laravel's [Tinker REPL](https://laravel.com/docs/8.x/artisan#tinker).
The short answer is "no", but I had a suggestion for them.

<!--- EXTENDED -->

## PHP REPL

The first part of my answer to the question was suggesting they use the PHP REPL.

PHP has had a REPL since version 5.1.0, which you can invoke using `php -a`.
Once you've started, you can do anything you'd normally do in PHP, including such things as including files, declaring namespaces, declaring classes, etc.

I've often wanted to test how classes work, or run one-off jobs without writing a script.
To accomplish these tasks, I'll include the autoloader installed by Composer:

```bash
$ php -a
php > include './vendor/autoload.php';
```

Once I've done that, I can reference any classes, functions, and constants autoloaded by Composer, as well as anything PHP exposes.

## Mezzio container

The Mezzio skeleton sets up a few files in its `config/` subdirectory that give us a number of ready-to use artifacts.

The first is `config/config.php`, which aggregates and returns all application configuration, including from [config providers](https://docs.laminas.dev/laminas-config-aggregator/config-providers/) as well as local autoloaded configuration files (those in `config/autload/`).
From the REPL, you can dump this information directly if you want:

```bash
$ php -a
php > include './vendor/autoload.php'; // we need autoloading for most config providers
php > $config = include './config/config.php';
php > var_export($config);
```

The second, and more important to this exercise, is `config/container.php`.
This file autoloads, configures, and returns the [PSR-11](https://www.php-fig.org/psr/psr-11/) DI container defined in the application.
By using this approach, we can keep Mezzio agnostic of the specific container used, and leave it to that file to properly instantiate and configure it for us.
We provide out-of-the-box versions of that file for a number of containers, and have a [specification for dependency configuration](https://docs.mezzio.dev/mezzio/v3/features/container/config/) that we recommend to ensure that dependency configuration provided by third-party packages can be detected and utilized.

Because this is just a PHP file, and returns the PSR-11 container, we can include that and capture it to a variable in the REPL:

```bash
$ php -a
php > include './vendor/autoload.php';
php > $container = include './config/container.php';
```

From there, you can then pull any configured services, including the configuration, and start interacting with them:

```bash
php > include './vendor/autoload.php';
php > $container = include './config/container.php';
php > $config = $container->get('config');
php > echo $config['debug'] ? 'In debug mode' : 'In production mode';
php > $httpClient = $container->get(Http\Adapter\Guzzle7\Client::class);
```

### Fin

While not quite as powerful as Tinker, the PHP REPL, coupled with Composer autoloading and a configured PSR-11 container, is a fantastic tool for interacting with your project.
I can definitely recommend this as a way to play with and experiment with your application code!
