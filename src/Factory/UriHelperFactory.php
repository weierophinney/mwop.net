<?php
namespace Mwop\Factory;

use Mwop\UriHelper;
use Zend\Expressive\Router\RouterInterface;

class UriHelperFactory
{
    public function __invoke($services)
    {
        return new UriHelper(
            $services->get(RouterInterface::class)
        );
    }
}
