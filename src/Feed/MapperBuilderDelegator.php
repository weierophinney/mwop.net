<?php

declare(strict_types=1);

namespace Mwop\Feed;

use CuyZ\Valinor\Mapper\Tree\Message\ThrowableMessage;
use CuyZ\Valinor\MapperBuilder;
use DateTimeInterface;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

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

        $builder->registerConstructor(
            /**
             * @param not-empty-string $title
             * @param not-empty-string $link
             * @param not-empty-string $sitename
             * @param not-empty-string $siteurl
             */
            function (
                string $title,
                string $link,
                string $sitename,
                string $siteurl,
                DateTimeInterface $created,
                null|string $favicon = null,
                null|string $author = null,
            ) use ($faviconMap): FeedItem {
                if (is_string($author) && ! preg_match('/phinney/i', $author)) {
                    return new InvalidFeedItem();
                }

                if (is_string($favicon) && preg_match('/^\s*$/', $favicon)) {
                    throw ThrowableMessage::new('"favicon" was not a non-empty-string', 'empty_string');
                }

                if (
                    null === $favicon
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
