<?php
use Zend\Stdlib\ArrayUtils;

$merged = array();
foreach (glob('config/autoload/{,*.}{global,local}.php', GLOB_BRACE) as $file) {
    $config = include $file;
    if (! is_array($config)) {
        continue;
    }

    $merged = ArrayUtils::merge($merged, $config);
}
return $merged;
