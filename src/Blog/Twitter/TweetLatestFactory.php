<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Mwop\App\Factory\PlatesFunctionsDelegator;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

class TweetLatestFactory
{
    public function __invoke(ContainerInterface $container): TweetLatest
    {
        $config = $container->get('config-blog.twitter');

        return new TweetLatest(
            $container->get(MapperInterface::class),
            $container->get(TwitterFactory::class),
            $container->get(PlatesFunctionsDelegator::class),
            $config['logo_path'],
        );
    }
}
