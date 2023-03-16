<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use CuyZ\Valinor\MapperBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

use function getcwd;
use function realpath;

class FeedFactory
{
    public function __invoke(ContainerInterface $container): Feed
    {
        /** @var MapperBuilder $builder */
        $builder = $container->get(MapperBuilder::class);
        Assert::isInstanceOf($builder, MapperBuilder::class);

        return new Feed(
            feedPath: realpath(getcwd()) . '/data/shared/mastodon.json',
            mapper: $builder->mapper(),
            logger: $container->get(LoggerInterface::class),
        );
    }
}
