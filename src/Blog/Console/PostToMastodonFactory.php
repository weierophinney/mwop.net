<?php

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;

class PostToMastodonFactory
{
    public function __invoke(ContainerInterface $container): PostToMastodon
    {
        $config = $container->get('config-blog.api');
        return new PostToMastodon(
            $container->get(RequestFactoryInterface::class),
            $config['token_header'],
        );
    }
}
