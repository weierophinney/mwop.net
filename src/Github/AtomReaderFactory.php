<?php

declare(strict_types=1);

namespace Mwop\Github;

use Laminas\Feed\Reader\Entry\EntryInterface;
use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Laminas\Feed\Reader\Reader as FeedReader;
use Laminas\Feed\Reader\StandaloneExtensionManager;
use Psr\Container\ContainerInterface;

use function preg_match;

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
        $reader->addFilter(
            // phpcs:ignore Generic.Files.LineLength.TooLong
            fn (EntryInterface $entry): bool => preg_match('#weierophinney/#', $entry->getLink()) ? false : true
        );

        return $reader;
    }
}
