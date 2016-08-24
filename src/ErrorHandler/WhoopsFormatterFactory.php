<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\ErrorHandler;

use Interop\Container\ContainerInterface;
use Mwop\ErrorHandler;
use Zend\Expressive\Whoops;
use Zend\Expressive\WhoopsPageHandler;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

class WhoopsFormatterFactory implements DelegatorFactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        callable $callback,
        array $options = null
    ) : ErrorHandler {
        $errorHandler = $callback();

        $errorHandler->setErrorFormatter(
            new WhoopsFormatter(
                $container->get(Whoops::class),
                $container->get(WhoopsPageHandler::class)
            )
        );

        return $errorHandler;
    }
}
