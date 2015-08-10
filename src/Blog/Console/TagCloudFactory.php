<?php
namespace Mwop\Blog\Console;

class TagCloudFactory
{
    public function __invoke($services)
    {
        return new TagCloud($services->get('Mwop\Blog\Mapper'));
    }
}
