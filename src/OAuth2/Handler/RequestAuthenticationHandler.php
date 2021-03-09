<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\OAuth2\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Mwop\OAuth2\Provider\ProviderFactory;
use Mwop\OAuth2\RenderUnauthorizedResponseTrait;
use Mwop\OAuth2\ValidateProviderTrait;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAuthenticationHandler implements RequestHandlerInterface
{
    use RenderUnauthorizedResponseTrait;
    use ValidateProviderTrait;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        private ProviderFactory $providerFactory,
        TemplateRendererInterface $renderer,
        bool $isDebug
    ) {
        $this->responseFactory = $responseFactory;
        $this->renderer        = $renderer;
        $this->isDebug         = $isDebug;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $redirect = $request->getQueryParams()['redirect'] ?? $request->getUri()->withPath('/');

        $providerType = $request->getAttribute('provider');
        if (! $this->validateProvider($providerType)) {
            return $this->renderUnauthorizedResponse(
                $request,
                $redirect,
                'Invalid authentication provider'
            );
        }

        $provider         = $this->providerFactory->createProvider($providerType);
        $authorizationUrl = $provider->getAuthorizationUrl();

        $session = $request->getAttribute('session');
        $session->set('auth', [
            'state'    => $provider->getState(),
            'redirect' => $redirect,
        ]);

        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', $authorizationUrl);
    }
}
