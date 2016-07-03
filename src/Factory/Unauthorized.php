<?php
namespace Mwop\Factory;

use Mwop\Unauthorized as Middleware;
use Zend\Expressive\Template\TemplateRendererInterface;

class Unauthorized
{
    public function __invoke($container)
    {
        return new Middleware(
            $container->get(TemplateRendererInterface::class)
        );
    }
}
