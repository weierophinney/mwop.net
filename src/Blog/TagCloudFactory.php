<?php
namespace Mwop\Blog;

class TagCloudFactory
{
    public function __invoke($services)
    {
        return new TagCloud(
            $services->get('Mwop\Blog\Mapper'),
            $services->get('renderer')
        );
    }
}
