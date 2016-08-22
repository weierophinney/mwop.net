<?php
namespace Mwop\Contact;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ProcessFactory
{
    public function __invoke(ContainerInterface $container) : Process
    {
        return new Process(
            $container->get('session'),
            $container->get('mail.transport'),
            $container->get(TemplateRendererInterface::class),
            $container->get('config')['contact']
        );
    }
}
