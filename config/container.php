<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

// Load configuration
$config = require 'config.php';

// Build container
$container = new ServiceManager($config['dependencies']);

// Inject config
$container->setService('config', $config);

return $container;
