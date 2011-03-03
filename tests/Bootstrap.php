<?php
set_include_path(implode(PATH_SEPARATOR, array(
    '.',
    get_include_path(),
)));

$_baseDir = realpath(__DIR__ . '/../');
require_once $_baseDir . '/library/zf2/Zend/Loader/ClassMapAutoloader.php';
$loader = new Zend\Loader\ClassMapAutoloader(array(
    $_baseDir . '/library/.classmap.php',
    $_baseDir . '/application/.classmap.php',
));
$loader->register();
unset($_baseDir);
