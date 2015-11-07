<?php
use Zend\Config\Factory as Config;

return Config::fromFiles([
    'config/autoload/global.php',
    'config/autoload/local.php',
]);
