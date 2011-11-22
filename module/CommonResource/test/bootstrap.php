<?php
include __DIR__ . '/../autoload_register.php';

set_include_path(implode(PATH_SEPARATOR, array(
    '.',
    get_include_path(),
)));
spl_autoload_register(function($class) {
    $file = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
    if (false === ($realpath = stream_resolve_include_path($file))) {
        return false;
    }
    include_once $realpath;
});
