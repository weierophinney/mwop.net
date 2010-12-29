<?php
set_include_path(implode(PATH_SEPARATOR, array(
    '.',
    realpath(__DIR__ . '/../library'),
    get_include_path(),
)));

function autoloader($class) 
{
    $file = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
    return include_once $file;
};
spl_autoload_register('autoloader');

