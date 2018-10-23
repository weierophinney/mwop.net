<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Zend\Diactoros\Response\HtmlResponse;

class CallbackHandler implements RequestHandlerInterface
{
    use ValidateProviderTrait;

    /**
     * @var ProviderFactory
     */
    private $providerFactory;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ProviderFactory $providerFactory,
        bool $isDebug
    ) {
        $this->responseFactory = $responseFactory;
        $this->providerFactory = $providerFactory;
        $this->isDebug = $isDebug;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $providerType = $request->getAttribute('provider');
        if (! $this->validateProvider()) {
            throw new RuntimeException(sprintf(
                'Invalid provider provided to %s',
                __CLASS__
            ));
        }

        $params = $request->getQueryParams();
        $error = $params['error'] ?? false;
        if ($error) {
            throw new RuntimeException(sprintf(
                'Error occurred during authentication: %s',
                var_export($error, true)
            ));
        }

        $code = $params['code'] ?? false;
        if (! $code) {
            // No code returned from provider?
            return $this->responseFactory->createResponse(301)
                ->withHeader('Location', '/auth/' . $provider);
        }

        $session = $request->getAttribute('session');
        $sessionData = $session->get('auth', []);

        $state = $params['state'] ?? '';
        if (empty($state)
            || ! isset($sessionData['state'])
            || $state !== $sessionData['state']
        ) {
            // No state returned from provider, or mismatched state; start over
            return $this->responseFactory->createResponse(301)
                ->withHeader('Location', '/auth/' . $provider);
        }

        // Attempt to retrieve the access token.
        // This will raise an exception if it cannot.
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);

        $resourceOwner = $provider->getResourceOwner($token);

        // Authenticated! Store details in session so we can redirect to the
        // page requesting authorization.
        $sessionData['user'] = array_merge(
            $resourceOwner->toArray(),
            ['username' => $this->getUsernameFromResourceOwner($resourceOwner)]
        );

        $redirect = $sessionData['redirect'] ?? '/';
        unset($sessionData['redirect'], $sessionData['state']);

        $session->set('auth', $sessionData);
        return $this->responseFactory->createResponse(301)
            ->withHeader('Location', $redirect);
    }
}
