<?php

declare(strict_types=1);

namespace Mwop\Feed;

use CuyZ\Valinor\MapperBuilder;
use DateTimeInterface;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

use function array_key_exists;
use function preg_match;

class MapperBuilderDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): MapperBuilder {
        /** @var MapperBuilder $builder */
        $builder = $factory();

        $faviconMap = $container->get('config')['feeds']['favicon-map'] ?? [];
        Assert::isMap($faviconMap);

        // The MapperBuilder class is immutable, so each method returns a new
        // instance. As such, you need to capture it.
        $builder = $builder->infer(
            FeedItem::class,
            /** @return class-string<InvalidFeedItem|PopulatedFeedItem> */
            fn (string $author): string => preg_match('/phinney/i', $author)
                    ? PopulatedFeedItem::class
                    : InvalidFeedItem::class,
        );

        $builder = $builder->registerConstructor(
            fn (
                string $title,
                string $link,
                string $sitename,
                string $siteurl,
                DateTimeInterface $created,
                string $favicon,
            ): InvalidFeedItem => new InvalidFeedItem(),
            /**
             * @param non-empty-string $title
             * @param non-empty-string $link
             * @param non-empty-string $sitename
             * @param non-empty-string $siteurl
             */
            function (
                string $title,
                string $link,
                string $sitename,
                string $siteurl,
                DateTimeInterface $created,
                string $favicon,
            ) use ($faviconMap): PopulatedFeedItem {
                if (
                    empty($favicon)
                    && array_key_exists($siteurl, $faviconMap)
                ) {
                    $favicon = $faviconMap[$siteurl];
                }

                return new PopulatedFeedItem($title, $link, (string) $favicon, $sitename, $siteurl, $created);
            },
        );

        return $builder;
    }
}
