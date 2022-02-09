<?php

declare(strict_types=1);

namespace Mwop\Blog\Flickr;

use JeroenG\Flickr\Flickr;
use Psr\Container\ContainerInterface;

class PhotosFactory
{
    public function __invoke(ContainerInterface $container): Photos
    {
        return new Photos($container->get(Flickr::class));
    }
}
