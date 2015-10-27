<?php
namespace Mwop\Blog\Console;

class FeedGeneratorFactory
{
    public function __invoke($services)
    {
        return new FeedGenerator(
            $services->get('Mwop\Blog\Mapper'),
            realpath(getcwd()) . '/data/blog/authors/'
        );
    }
}
