<?php
use Zend\Config\Factory as Config;

/**
 * Trick zf-deploy into thinking this is a ZF2 app so it can build a package.
 *
 * 'modules' => array(
 * )
 */

return Config::fromFiles(
    glob('config/autoload/{,*.}{global,local}.php', GLOB_BRACE)
);
