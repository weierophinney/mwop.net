<?php
namespace Mwop\Contact;

use Zend\Expressive\Template\TemplateRendererInterface;

class ProcessFactory
{
    public function __invoke($container)
    {
        return new Process(
            $container->get('session'),
            $container->get('mail.transport'),
            $container->get(TemplateRendererInterface::class),
            $container->get('config')['contact']
        );
    }
}
