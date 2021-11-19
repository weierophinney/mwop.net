<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
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
            $container->get(ServerUrlHelper::class),
            $container->get(UrlHelper::class),
            $config['logo_path'],
        );
    }
}
