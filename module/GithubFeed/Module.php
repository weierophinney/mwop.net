<?php

namespace GithubFeed;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array('factories' => array(
            'GithubFeed\AtomReader' => function ($services) {
                $config = $services->get('config');
                $config = $config['github_feed'];
                $reader = new AtomReader($config['user'], $config['token']);
                if (isset($config['limit'])) {
                    $reader->setLimit($config['limit']);
                }
                return $reader;
            },
        ));
    }
}
