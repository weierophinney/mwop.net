<?php

declare(strict_types=1);

namespace Mwop\Github;

use CuyZ\Valinor\MapperBuilder;
use Laminas\Feed\Reader\Entry\EntryInterface;
use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Laminas\Feed\Reader\Reader as FeedReader;
use Laminas\Feed\Reader\StandaloneExtensionManager;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

use function preg_match;

class AtomReaderFactory
{
    public function __invoke(ContainerInterface $container): AtomReader
    {
        /** @var MapperBuilder $builder */
        $builder = $container->get(MapperBuilder::class);
        Assert::isInstanceOf($builder, MapperBuilder::class);

        $http = $container->get(FeedReaderHttpClientInterface::class);
        FeedReader::setHttpClient($http);
        FeedReader::setExtensionManager(new StandaloneExtensionManager());

        $config = $container->get('config-github');

        $reader = new AtomReader($config['user'], $builder->mapper());
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
