<?php

declare(strict_types=1);

use Laminas\ServiceManager\ServiceManager;

// Load configuration
$config = require 'config.php';

// Build container
$container = new ServiceManager($config['dependencies']);

// Inject config
$container->setService('config', $config);

return $container;
