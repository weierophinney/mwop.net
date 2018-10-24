<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Zend\Expressive\Template\TemplateRendererInterface;

class CallbackHandler implements RequestHandlerInterface
{
    use ValidateProviderTrait;
    use RenderUnauthorizedResponseTrait;

    /**
     * @var bool
     */
    private $isDebug = false;

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
        if (! $this->validateProvider($providerType)) {
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
        if (empty($state)) {
            // No state returned from provider
            return $this->renderUnauthorizedResponse(
                $request,
                $redirect,
                'Missing provider state'
            );
        }

        if (! isset($sessionData['state'])) {
            // No state in session
            return $this->renderUnauthorizedResponse(
                $request,
                $redirect,
                'Missing initial state'
            );
        }

        if ($state !== $sessionData['state']) {
            // State mismatch
            return $this->renderUnauthorizedResponse(
                $request,
                $redirect,
                'Mismatched provider state'
            );
        }

        // Attempt to retrieve the access token.
        $provider = $this->providerFactory->createProvider($providerType);
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


    /**
     * @throws Exception\UnexpectedResourceOwnerTypeException if unable to determine
     *     username from resource owner.
     */
    private function getUsernameFromResourceOwner(ResourceOwnerInterface $resourceOwner) : string
    {
        if (method_exists($resourceOwner, 'getEmail')) {
            // All official providers except Instagram
            return $resourceOwner->getEmail();
        }

        if (method_exists($resourceOwner, 'getNickname')) {
            // Instagram
            return $resourceOwner->getNickname();
        }

        if ($resourceOwner instanceof DebugResourceOwner) {
            return $resourceOwner->getId();
        }

        throw Exception\UnexpectedResourceOwnerTypeException::forResourceOwner($resourceOwner);
    }
}
