<?php
use Zend\Stdlib\ArrayUtils;

/**
 * Trick zf-deploy into thinking this is a ZF2 app so it can build a package.
 *
 * 'modules' => array(
 * )
 */

$merged = array();
foreach (glob('config/autoload/{,*.}{global,local}.php', GLOB_BRACE) as $file) {
    $config = include $file;
    if (! is_array($config)) {
        continue;
    }

    $merged = ArrayUtils::merge($merged, $config);
}
return $merged;
