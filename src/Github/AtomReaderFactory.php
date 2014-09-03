<?php
namespace Mwop\Github;

use Zend\Feed\Reader\Reader as FeedReader;

class AtomReaderFactory
{
    public function __invoke($services)
    {
        $http   = $services->get('http');
        FeedReader::setHttpClient($http);
        FeedReader::setExtensionManager($this->createExtensionManager());

        $config = $services->get('Config');
        $config = $config['github'];

        $reader = new AtomReader($config['user'], $config['token']);
        $reader->setLimit($config['limit']);
        return $reader;
    }

    private function createExtensionManager()
    {
        return new AtomReaderExtensions();
    }
}
