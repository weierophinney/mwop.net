<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\DefaultUser;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class CheckAuthenticationMiddleware implements MiddlewareInterface
{
    use RenderUnauthorizedResponseTrait;

    public function __construct(
        TemplateRendererInterface $renderer,
        ResponseFactoryInterface $responseFactory,
        bool $isDebug
    ) {
        $this->renderer = $renderer;
        $this->responseFactory = $responseFactory;
        $this->isDebug = $isDebug;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $session = $request->getAttribute('session');
        if (! $session) {
            // No session, thus no user; render an unauthorized response
            return $this->renderUnauthorizedResponse($request);
        }

        $authData = $session->get('auth', []);
        $user = $authData['user'] ?? false;

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
