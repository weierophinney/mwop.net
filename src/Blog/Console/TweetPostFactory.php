<?php

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;

class TweetPostFactory
{
    public function __invoke(ContainerInterface $container): TweetPost
    {
        $config = $container->get('config-blog.api');
        return new TweetPost(
            $container->get(RequestFactoryInterface::class),
            $config['token_header'],
        );
    }
}
