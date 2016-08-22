<?php
namespace Mwop\Contact;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LandingPageFactory
{
    public function __invoke(ContainerInterface $container) : LandingPage
    {
        return new LandingPage(
            $container->get(TemplateRendererInterface::class),
            $container->get('session'),
            $container->get('config')['contact']
        );
    }
}
