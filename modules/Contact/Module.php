<?php

namespace Contact;

use InvalidArgumentException,
    Zend\Module\Consumer\AutoloaderProvider;

class Module implements AutoloaderProvider
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
        );
    }

    public function getConfig($env = null)
    {
        $config = include __DIR__ . '/configs/module.config.php';
        if (null === $env) {
            return $config;
        }

        if (!isset($config[$env])) {
            throw new InvalidArgumentException(sprintf(
                'Unrecognized environment "%s" provided to %s',
                $env,
                __METHOD__
            ));
        }

        return $config[$env];
    }

    public function getProvides()
    {
        return array(
            'name'    => 'Blog',
            'version' => '0.1.0',
        );
    }

    public function getDependencies()
    {
        return array(
            'php' => array(
                'required' => true,
                'version'  => '>=5.3.1',
            ),
            'ext/mongo' => array(
                'required' => true,
                'version'  => '>=1.2.0',
            ),
            'CommonResource' => array(
                'required' => true,
                'version'  => '>=0.1.0',
            )
        );
    }
}
