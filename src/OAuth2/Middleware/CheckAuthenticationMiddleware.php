<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\OAuth2\Middleware;

use Mezzio\Authentication\DefaultUser;
use Mezzio\Authentication\UserInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\OAuth2\RenderUnauthorizedResponseTrait;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CheckAuthenticationMiddleware implements MiddlewareInterface
{
    use RenderUnauthorizedResponseTrait;

    public function __construct(
        TemplateRendererInterface $renderer,
        ResponseFactoryInterface $responseFactory,
        bool $isDebug
    ) {
        $this->renderer        = $renderer;
        $this->responseFactory = $responseFactory;
        $this->isDebug         = $isDebug;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $request->getAttribute('session');
        if (! $session) {
            // No session, thus no user; render an unauthorized response
            return $this->renderUnauthorizedResponse($request);
        }

        $authData = $session->get('auth', []);
        $user     = $authData['user'] ?? false;

        if (! $user) {
            // No user found in session data; render an unauthorized response
            return $this->renderUnauthorizedResponse($request);
        }

        return $handler->handle($request->withAttribute(
            UserInterface::class,
            new DefaultUser($user['username'], [], $user)
        ));
    }
}
