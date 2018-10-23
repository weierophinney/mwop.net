<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAuthenticationHandler implements RequestHandlerInterface
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
        $redirect = $request->getQueryParams()['redirect'] ?? $request->getUri()->withPath('/');

        $providerType = $request->getAttribute('provider');
        if (! $this->validateProvider()) {
            return $this->responseFactory->createResponse(302)
                ->withHeader('Location', $redirect);
        }

        $provider = $this->providerFactory->creatProvider($providerType);
        $authorizationUrl = $provider->getAuthorizationUrl();

        $session = $request->getAttribute('session');
        $session->set('auth', [
            'state' => $provider->getState(),
            'redirect' => $redirect,
        ]);

        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', $authorizationUrl);
    }
}
