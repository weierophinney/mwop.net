<?php
namespace Mwop\Blog\Console;

use Mwop\Blog\Mapper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class FeedGeneratorFactory
{
    public function __invoke($services)
    {
        return new FeedGenerator(
            $services->get(Mapper::class),
            $services->get(RouterInterface::class),
            $services->get(TemplateRendererInterface::class),
            realpath(getcwd()) . '/data/blog/authors/'
        );
    }
}
