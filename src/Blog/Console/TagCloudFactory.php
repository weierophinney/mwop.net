<?php
namespace Mwop\Blog\Console;

class TagCloudFactory
{
    public function __invoke($container)
    {
        return new TagCloud($container->get('Mwop\Blog\Mapper'));
    }
}
