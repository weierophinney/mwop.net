<?php
namespace Mwop;

function createServiceContainer($config)
{
    $services = new Services;
    $services->add('Config', $config);

    $config = $config['services'];

    foreach ($config['invokables'] as $name => $class) {
        $services->add($name, $class);
    }

    foreach ($config['factories'] as $name => $factory) {
        $services->add($name, new $factory);
    }

    return $services;
}
