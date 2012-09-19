<?php

namespace GithubFeed;

use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Http\Client as HttpClient;

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
            'GithubFeed\HttpClient' => function ($services) {
                $client = new HttpClient();
                $client->setOptions(array(
                    'adapter' => 'Zend\Http\Client\Adapter\Curl',
                ));
                return $client;
            },
            'GithubFeed\AtomReader' => function ($services) {
                $config = $services->get('config');
                $client = $services->get('GithubFeed\HttpClient');

                FeedReader::setHttpClient($client);

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
