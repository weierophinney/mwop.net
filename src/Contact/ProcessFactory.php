<?php
namespace Mwop\Contact;

use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ProcessFactory
{
    public function __invoke($services)
    {
        return new Process(
            $services->get('session'),
            $services->get('mail.transport'),
            $services->get(TemplateRendererInterface::class),
            $services->get(RouterInterface::class),
            $services->get('config')['contact']
        );
    }
}
