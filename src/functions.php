<?php
namespace Mwop;

function createServiceContainer($config)
{
    $services = new Services;
    $services->add('Config', $config);

    $config = $config['services'];

    foreach ($config['invokables'] as $class) {
        $services->add($class, $class);
    }

    foreach ($config['factories'] as $name => $factory) {
        $services->add($name, new $factory);
    }

    return $services;
}

function getPhpExecutable()
{
    $php = (strtouper(substr(PHP_OS, 0, 3)) === 'WIN')
        ? escapeshellarg('c:/Program Files (x86)/ZendServer/bin/php.exe')
        : '/usr/local/zend/bin/php';
    return $php . ' -d date.timezone=America/Chicago';
}
