<?php
namespace Mwop\Factory;

use Mwop\Blog\EntryView;
use Phly\Expressive\Mustache\MustacheTemplate;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UriTemplateDelegatorFactory implements DelegatorFactoryInterface
{
    public function createDelegatorWithName(ServiceLocatorInterface $services, $name, $requestedName, $callback)
    {
        $renderer = $callback();

        if (! $renderer instanceof MustacheTemplate) {
            return $renderer;
        }

        $renderer->attachParamListener(function ($vars, array $defaults) {
            if (! $vars instanceof EntryView) {
                return;
            }

            foreach ($defaults as $key => $value) {
                if ($key === 'uri') {
                    $vars->uriHelper = $value;
                    continue;
                }
                $vars->{$key} = $value;
            }

            return $vars;
        });

        return $renderer;
    }
}
