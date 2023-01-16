<?php

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;

class PostLatestToMastodonFactory
{
    public function __invoke(ContainerInterface $container): PostLatestToMastodon
    {
        $config = $container->get('config-blog.api');
        return new PostLatestToMastodon(
            $container->get(RequestFactoryInterface::class),
            $config['token_header'],
        );
    }
}
