<?php
namespace Mwop\Factory;

use Mwop\ErrorHandler;
use Zend\Expressive\Template\TemplateRendererInterface;

class ErrorHandlerFactory
{
    public function __invoke($container, $canonicalName, $requestedName)
    {
        $config = $container->get('config');
        $displayErrors = array_key_exists('debug', $config)
            ? (bool) $config['debug']
            : false;
        return new ErrorHandler(
            $container->get(TemplateRendererInterface::class),
            $displayErrors
        );
    }
}
