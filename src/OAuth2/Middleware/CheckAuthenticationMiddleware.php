<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\OAuth2\Middleware;

use Mwop\OAuth2\RenderUnauthorizedResponseTrait;
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

    /**
     * @var bool
     */
    private $isDebug = false;

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
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
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
