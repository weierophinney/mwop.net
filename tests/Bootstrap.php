<?php
set_include_path(implode(PATH_SEPARATOR, array(
    '.',
    get_include_path(),
)));

$_baseDir = realpath(__DIR__ . '/../library');
require_once $_baseDir . '/Zend/Loader/ClassMapAutoloader.php';
$loader = new Zend\Loader\ClassMapAutoloader(array(
    $_baseDir . '/mwop/.classmap.php',
    $_baseDir . '/Zend/.classmap.php',
));
$loader->register();
unset($_baseDir);
