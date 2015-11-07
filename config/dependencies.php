<?php
use Zend\Config\Factory as Config;

return Config::fromFiles([
    'config/autoload/dependencies.global.php',
    'config/autoload/dependencies.local.php',
]);
