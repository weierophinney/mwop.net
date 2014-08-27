<?php
namespace Mwop;

use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

function createServiceContainer($config)
{
    $configuration = new Config($config);
    $services = new ServiceManager();
    $configuration->configureServiceManager($services);
    return $services;
}
