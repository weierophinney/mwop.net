<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Psr\Container\ContainerInterface;

class TwitterFactoryFactory
{
    public function __invoke(ContainerInterface $container): TwitterFactory
    {
        $config = $container->get('config-blog.twitter');
        return new TwitterFactory(
            $config['consumer_key'],
            $config['consumer_secret'],
            $config['access_token'],
            $config['access_token_secret'],
        );
    }
}
