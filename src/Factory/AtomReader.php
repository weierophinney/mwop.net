<?php
namespace Mwop\Factory;

use Mwop\Github\AtomReader as Reader;
use Mwop\Github\AtomReaderExtensions;
use Zend\Feed\Reader\Reader as FeedReader;

class AtomReader
{
    public function __invoke($services)
    {
        $http   = $services->get('http');
        FeedReader::setHttpClient($http);
        FeedReader::setExtensionManager($this->createExtensionManager());

        $config = $services->get('Config');
        $config = $config['github'];

        $reader = new Reader($config['user'], $config['token']);
        $reader->setLimit($config['limit']);
        return $reader;
    }

    private function createExtensionManager()
    {
        return new AtomReaderExtensions();
    }
}
