<?php

declare(strict_types=1);

namespace Mwop\Blog\Flickr;

use JeroenG\Flickr\Api;
use JeroenG\Flickr\Flickr;
use Psr\Container\ContainerInterface;

class FlickrFactory
{
    public function __invoke(ContainerInterface $container): Flickr
    {
        $config = $container->get('config');
        $apiKey = $config['flickr']['api_key'] ?? '';
        return new Flickr(new Api($apiKey));
    }
}
