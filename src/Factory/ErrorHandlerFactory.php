<?php
namespace Mwop\Factory;

use Mwop\ErrorHandler;
use Zend\Expressive\Template\TemplateRendererInterface;

class ErrorHandlerFactory
{
    public function __invoke($services, $canonicalName, $requestedName)
    {
        $config = $services->get('config');
        $displayErrors = array_key_exists('debug', $config)
            ? (bool) $config['debug']
            : false;
        return new ErrorHandler(
            $services->get(TemplateRendererInterface::class),
            $displayErrors
        );
    }
}
