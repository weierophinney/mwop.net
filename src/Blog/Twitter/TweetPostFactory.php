<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Mezzio\Helper\UrlHelper;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

class TweetPostFactory
{
    public function __invoke(ContainerInterface $container): TweetPost
    {
        $config = $container->get('config-blog.twitter');

        return new TweetPost(
            blogPostMapper: $container->get(MapperInterface::class),
            factory: $container->get(TwitterFactory::class),
            urlHelper: $container->get(UrlHelper::class),
            logoPath: $config['logo_path'],
        );
    }
}
