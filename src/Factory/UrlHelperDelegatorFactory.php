<?php
namespace Mwop\Factory;

use Zend\Expressive\Application;
use Zend\Expressive\Helper\UrlHelper;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UrlHelperDelegatorFactory implements DelegatorFactoryInterface
{
    public function createDelegatorWithName(ServiceLocatorInterface $services, $name, $requestedName, $callback)
    {
        $application = $callback();
        $application->attachRouteResultObserver($services->get(UrlHelper::class));
        return $application;
    }
}
