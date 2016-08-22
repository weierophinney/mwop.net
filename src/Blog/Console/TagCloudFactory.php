<?php
namespace Mwop\Blog\Console;

use Interop\Container\ContainerInterface;

class TagCloudFactory
{
    public function __invoke(ContainerInterface $container) : TagCloud
    {
        return new TagCloud($container->get('Mwop\Blog\Mapper'));
    }
}
