<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Github;

use Psr\Container\ContainerInterface;
use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Feed\Reader\StandaloneExtensionManager;

use function strpos;

class AtomReaderFactory
{
    public function __invoke(ContainerInterface $container) : AtomReader
    {
        $http = $container->get(FeedReaderHttpClientInterface::class);
        FeedReader::setHttpClient($http);
        FeedReader::setExtensionManager(new StandaloneExtensionManager());

        $config = $container->get('config');
        $config = $config['github'];

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
