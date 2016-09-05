<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Interop\Container\ContainerInterface;
use Mwop\ErrorHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

class FinalHandlerFactory
{
    public function __invoke(ContainerInterface $container) : callable
    {
        return function (Request $request, Response $response, $err = null) use ($container) : Response {
            if ($err instanceof Throwable) {
                $errorHandler = $container->get(ErrorHandler::class);
                return $errorHandler->createErrorResponse($err, $request);
            }

            if ($err) {
                error_log(sprintf("FinalHandler received a non-throwable error: %s", var_export($err, true)));
            }

            return $response;
        };
    }
}
