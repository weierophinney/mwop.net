<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Expressive\Template\TemplateRendererInterface;

class UnauthorizedResponseFactoryFactory
{
    const TEMPLATE = 'oauth2clientauthentication::401';

    public function __invoke(ContainerInterface $container) : callable
    {
        return function (Request $request) use ($container) : Response {
            $originalRequest = $request->getAttribute('originalRequest', $request);

            $config = $container->get('config');
            $debug  = $config['debug'] ?? false;

            $view = [
                'auth_path' => (string) $request->getUri()->withPath('/auth'),
                'redirect'  => (string) $originalRequest->getUri(),
                'debug'     => (bool) $debug,
            ];

            $response = $container->get(Response::class);
            $renderer = $container->get(TemplateRendererInterface::class);

            $response->getBody()->write(
                $renderer->render(self::TEMPLATE, $view)
            );
            return $response->withStatus(401);
        };
    }
}
