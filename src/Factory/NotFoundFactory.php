<?php
namespace Mwop\Factory;

use Mwop\NotFound;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class NotFoundFactory
{
    public function __invoke($services, $canonicalName, $requestedName)
    {
        return new NotFound(
            $services->get(TemplateRendererInterface::class),
            $services->get(RouterInterface::class)
        );
    }
}
