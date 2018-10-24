<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class CallbackHandler implements RequestHandlerInterface
{
    use ValidateProviderTrait;
    use RenderUnauthorizedResponseTrait;

    /**
     * @var ProviderFactory
     */
    private $providerFactory;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ProviderFactory $providerFactory,
        TemplateRendererInterface $renderer,
        bool $isDebug
    ) {
        $this->responseFactory = $responseFactory;
        $this->providerFactory = $providerFactory;
        $this->renderer = $renderer;
        $this->isDebug = $isDebug;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $session = $request->getAttribute('session');
        $sessionData = $session->get('auth', []);
        $redirect = $sessionData['redirect'] ?? null;

        $providerType = $request->getAttribute('provider');
        if (! $this->validateProvider()) {
            // Invalid provider
            return $this->renderUnauthorizedResponse(
                $request,
                $redirect,
                'Invalid authentication provider'
            );
        }

        $params = $request->getQueryParams();
        $error = $params['error'] ?? false;
        if ($error) {
            // Error during authentication with provider
            return $this->renderUnauthorizedResponse(
                $request,
                $redirect,
                sprintf('Authentication error reported by provider: %s', $error)
            );
        }

        $code = $params['code'] ?? false;
        if (! $code) {
            // No code returned from provider
            return $this->renderUnauthorizedResponse(
                $request,
                $redirect,
                'Missing authorization code from provider'
            );
        }

        $state = $params['state'] ?? '';
        if (empty($state)
            || ! isset($sessionData['state'])
            || $state !== $sessionData['state']
        ) {
            // No state returned from provider, or mismatched state
            return $this->renderUnauthorizedResponse(
                $request,
                $redirect,
                'Missing or mismatched provider state'
            );
        }

        // Attempt to retrieve the access token.
        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);
            $resourceOwner = $provider->getResourceOwner($token);
        } catch (IdentityProviderException $e) {
            return $this->renderUnauthorizedResponse(
                $request,
                $redirect,
                sprintf('Error retrieving access token from provider: %s', $e->getMessage())
            );
        }

        // Authenticated! Store details in session so we can redirect to the
        // page requesting authorization.
        $sessionData['user'] = array_merge(
            $resourceOwner->toArray(),
            ['username' => $this->getUsernameFromResourceOwner($resourceOwner)]
        );

        // Remove the redirect and state; no longer useful
        unset($sessionData['redirect'], $sessionData['state']);

        // Inject the session data into the session and redirect
        $session->set('auth', $sessionData);
        return $this->responseFactory->createResponse(301)
            ->withHeader('Location', $redirect ?? '/');
    }
}
