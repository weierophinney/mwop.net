<?php
namespace Mwop\Blog\Console;

use Interop\Container\ContainerInterface;
use Mwop\Blog\Mapper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class FeedGeneratorFactory
{
    public function __invoke(ContainerInterface $container) : FeedGenerator
    {
        return new FeedGenerator(
            $container->get(Mapper::class),
            $container->get(RouterInterface::class),
            $container->get(TemplateRendererInterface::class),
            realpath(getcwd()) . '/data/blog/authors/'
        );
    }
}
