<?php

namespace GithubFeed;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Http\Client as HttpClient;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\View\Renderer\PhpRenderer;

class Module implements ConsoleUsageProviderInterface
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
            'GithubFeed\Renderer' => function ($services) {
                $helpers  = $services->get('ViewHelperManager');
                $resolver = $services->get('ViewResolver');

                $renderer = new PhpRenderer();
                $renderer->setHelperPluginManager($helpers);
                $renderer->setResolver($resolver);

                return $renderer;
            },
        ));
    }

    public function getControllerConfig()
    {
        return array('factories' => array(
            'GithubFeed\Fetch' => function ($controllers) {
                $services = $controllers->getServiceLocator();
                $config   = $services->get('Config');
                $config   = $config['github_feed'];

                $controller = new FetchController();
                $controller->setConsole($services->get('Console'));
                $controller->setFeedFile($config['content_path']);
                $controller->setReader($services->get('GithubFeed\AtomReader'));
                $controller->setRenderer($services->get('GithubFeed\Renderer'));

                return $controller;
            },
        ));
    }

    public function getConsoleUsage(Console $console)
    {
        return array(
            'githubfeed fetch' => 'Fetch and cache Github activity',
        );
    }
}
