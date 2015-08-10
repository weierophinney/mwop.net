<?php
namespace Mwop\Blog\Console;

class CachePostsFactory
{
    public function __invoke($services)
    {
        return new CachePosts($services->get('Mwop\Blog\DisplayPostMiddleware'));
    }
}
