<?php

declare(strict_types=1);

namespace Mwop\Blog\Mastodon;

use Mezzio\Helper\UrlHelper;
use Mwop\Blog\Mapper\MapperInterface;
use Mwop\Mastodon\ApiClient;
use Mwop\Mastodon\Credentials;
use Psr\Container\ContainerInterface;

class PostLatestFactory
{
    public function __invoke(ContainerInterface $container): PostLatest
    {
        return new PostLatest(
            blogPostMapper: $container->get(MapperInterface::class),
            credentials: $container->get(Credentials::class),
            mastodon: $container->get(ApiClient::class),
            urlHelper: $container->get(UrlHelper::class),
        );
    }
}
