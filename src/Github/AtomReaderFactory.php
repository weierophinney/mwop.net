<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Github;

use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Laminas\Feed\Reader\Reader as FeedReader;
use Laminas\Feed\Reader\StandaloneExtensionManager;
use Psr\Container\ContainerInterface;

use function strpos;

class AtomReaderFactory
{
    public function __invoke(ContainerInterface $container): AtomReader
    {
        $http = $container->get(FeedReaderHttpClientInterface::class);
        FeedReader::setHttpClient($http);
        FeedReader::setExtensionManager(new StandaloneExtensionManager());

        $config = $container->get('config-github');

        $reader = new AtomReader($config['user']);
        $reader->setLimit($config['limit']);
        $reader->addFilter(function ($entry) {
            if (false !== strpos($entry->getLink(), 'weierophinney/mwop.net')) {
                return false;
            }
            return true;
        });

        return $reader;
    }
}
