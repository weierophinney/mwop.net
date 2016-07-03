<?php
namespace Mwop\Github;

use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\Feed\Reader\Reader as FeedReader;

class AtomReaderFactory
{
    public function __invoke($container)
    {
        $http   = $container->get(FeedReaderHttpClientInterface::class);
        FeedReader::setHttpClient($http);
        FeedReader::setExtensionManager($this->createExtensionManager());

        $config = $container->get('config');
        $config = $config['github'];

        $reader = new AtomReader($config['user']);
        $reader->setLimit($config['limit']);
        return $reader;
    }

    private function createExtensionManager()
    {
        return new AtomReaderExtensions();
    }
}
