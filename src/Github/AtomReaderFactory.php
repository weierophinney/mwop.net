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

        // phpcs:disable Generic.Files.LineLength.TooLong
        $reader->addFilter(
            fn (EntryInterface $entry): bool => preg_match('#weierophinney/#', $entry->getLink()) ? false : true
        );
        $reader->addFilter(
            fn (EntryInterface $entry): bool => preg_match('#weierophinney/#', $entry->getTitle()) ? false : true
        );
        $reader->addFilter(
            fn (EntryInterface $entry): bool => preg_match('#pushed to gh-pages#', $entry->getTitle()) ? false : true
        );
        // phpcs:enable Generic.Files.LineLength.TooLong

        return $reader;
    }
}
