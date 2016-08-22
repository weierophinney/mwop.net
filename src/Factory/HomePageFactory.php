<?php
namespace Mwop\Factory;

use Interop\Container\ContainerInterface;
use Mwop\Blog\Mapper;
use Mwop\HomePage;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageFactory
{
    public function __invoke(ContainerInterface $container) : HomePage
    {
        return new HomePage(
            $container->get('config')['homepage']['posts'] ?? [],
            $container->get(TemplateRendererInterface::class)
        );
    }
}
