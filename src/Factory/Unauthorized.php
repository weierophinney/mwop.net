<?php
namespace Mwop\Factory;

use Interop\Container\ContainerInterface;
use Mwop\Unauthorized as Middleware;
use Zend\Expressive\Template\TemplateRendererInterface;

class Unauthorized
{
    public function __invoke(ContainerInterface $container) : Middleware
    {
        return new Middleware(
            $container->get(TemplateRendererInterface::class)
        );
    }
}
