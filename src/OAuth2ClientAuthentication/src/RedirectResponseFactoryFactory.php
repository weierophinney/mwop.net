<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;

class RedirectResponseFactoryFactory
{
    const TEMPLATE = 'oauth2authentication::401';

    public function __invoke(ContainerInterface $container) : callable
    {
        return function (string $url) use ($container) : Response {
            $response = $container->get(Response::class);
            return $response
                ->withHeader('Location', $url)
                ->withStatus(302);
        };
    }
}
