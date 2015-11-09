<?php
namespace Mwop\Factory;

use Mwop\Blog\EntryView;
use Mwop\UriHelper;
use Phly\Expressive\Mustache\MustacheTemplate;
use Zend\Expressive\Template\TemplateRendererInterface;
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

        $renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'uri',
            $services->get(UriHelper::class)
        );

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
