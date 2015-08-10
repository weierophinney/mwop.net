<?php
use Zend\Config\Factory as Config;

return Config::fromFiles(
    glob('config/autoload/{,*.}{global,local}.php', GLOB_BRACE)
);
