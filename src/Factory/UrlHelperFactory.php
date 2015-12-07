<?php
namespace Mwop\Factory;

use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Router\RouterInterface;

class UrlHelperFactory
{
    public function __invoke($container)
    {
        return new UrlHelper($container->get(RouterInterface::class));
    }
}
