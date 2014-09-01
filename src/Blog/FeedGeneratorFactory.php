<?php
namespace Mwop\Blog;

class FeedGeneratorFactory
{
    public function __invoke($services)
    {
        return new FeedGenerator($services->get('Mwop\Blog\Mapper'));
    }
}
