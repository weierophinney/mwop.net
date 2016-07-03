<?php
namespace Mwop\Factory;

use Mwop\Blog\Mapper;
use Mwop\HomePage;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageFactory
{
    public function __invoke($container)
    {
        return new HomePage(
            $container->get(Mapper::class),
            $container->get(RouterInterface::class),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
