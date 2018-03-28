<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Console;

use Psr\Container\ContainerInterface;
use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Feed\Reader\StandaloneExtensionManager;

class FeedAggregatorFactory
{
    public function __invoke(ContainerInterface $container) : FeedAggregator
    {
        $http   = $container->get(FeedReaderHttpClientInterface::class);
        FeedReader::setHttpClient($http);
        FeedReader::setExtensionManager(new StandaloneExtensionManager());

        $config = $container->get('config');

        return new FeedAggregator(
            $config['homepage']['feeds'] ?? [],
            $config['homepage']['feed-count'] ?? 10
        );
    }
}
