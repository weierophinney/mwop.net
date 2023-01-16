<?php

declare(strict_types=1);

namespace Mwop\Art;

use Mezzio\Helper\UrlHelper;
use Mwop\Mastodon\ApiClient;
use Mwop\Mastodon\Credentials;
use Mwop\Mastodon\MediaFactory;
use Psr\Container\ContainerInterface;

final class PostToMastodonFactory
{
    public function __invoke(ContainerInterface $container): PostToMastodon
    {
        return new PostToMastodon(
            mastodon: $container->get(ApiClient::class),
            credentials: $container->get(Credentials::class),
            repo: $container->get(PhotoMapper::class),
            mediaFactory: $container->get(MediaFactory::class),
            urlHelper: $container->get(UrlHelper::class),
        );
    }
}
