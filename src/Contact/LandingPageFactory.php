<?php
namespace Mwop\Contact;

use Zend\Expressive\Template\TemplateRendererInterface;

class LandingPageFactory
{
    public function __invoke($container)
    {
        return new LandingPage(
            $container->get(TemplateRendererInterface::class),
            $container->get('session'),
            $container->get('config')['contact']
        );
    }
}
