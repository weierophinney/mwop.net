<?php

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Psr\Container\ContainerInterface;

class TweetLatestFactory
{
    public function __invoke(ContainerInterface $container): TWeetLatest
    {
        $config = $container->get('config-blog.api');
        return new TweetLatest($config['key']);
    }
}
