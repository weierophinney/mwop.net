<?php
require_once __DIR__ . '/../library/zf2/Zend/Loader/ClassMapAutoloader.php';
$classmap = new Zend\Loader\ClassMapAutoloader(array(
    __DIR__ . '/../library/.classmap.php',
    __DIR__ . '/../application/.classmap.php',
));
$classmap->register();

use Zend\Config\Json as JsonConfig,
    Zend\Di\DependencyInjectionContainer as DiC,
    Zend\Di\Configuration as DiConfig,
    Zend\Di\ContainerBuilder as DiBuilder;

$di = new DiC();
$injector = $di->getInjector();
$diconfig = new DiConfig($injector);
$config   = new JsonConfig(__DIR__ . '/../application/configs/di.json', 'development');
$diconfig->fromConfig($config);

$builder = new DiBuilder($injector);
$builder->setContainerClass('AppContext');
$container = $builder->getCodeGenerator(__DIR__ . '/../application/AppContext.php');
$container->write();
