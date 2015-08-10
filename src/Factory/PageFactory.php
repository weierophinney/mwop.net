<?php
namespace Mwop\Factory;

use Mwop\Page;

class PageFactory
{
    public function __invoke($services, $canonicalName, $requestedName)
    {
        return new Page(
            $this->deriveTemplateName($requestedName),
            [],
            $services->get('Mwop\Template\TemplateInterface')
        );
    }

    private function deriveTemplateName($service)
    {
        return strtolower(
            str_replace(
                '\\',
                '.',
                $service
            )
        );
    }
}
