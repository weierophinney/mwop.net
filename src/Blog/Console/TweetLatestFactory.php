<?php

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;

class TweetLatestFactory
{
    public function __invoke(ContainerInterface $container): TweetLatest
    {
        $config = $container->get('config-blog.api');
        return new TweetLatest(
            $container->get(RequestFactoryInterface::class),
            $config['token_header'],
        );
    }
}
