<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Mezzio\Helper\UrlHelper;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

class TweetLatestFactory
{
    public function __invoke(ContainerInterface $container): TweetLatest
    {
        $config = $container->get('config-blog.twitter');

        return new TweetLatest(
            blogPostMapper: $container->get(MapperInterface::class),
            factory: $container->get(TwitterFactory::class),
            urlHelper: $container->get(UrlHelper::class),
            logoPath: $config['logo_path'],
        );
    }
}
