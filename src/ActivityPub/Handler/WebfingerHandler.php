<?php

declare(strict_types=1);

namespace Mwop\ActivityPub\Handler;

use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Mwop\ActivityPub\Webfinger\AccountMap;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_exists;
use function preg_match;

class WebfingerHandler implements RequestHandlerInterface
{
    public function __construct(
        private AccountMap $accountMap,
        private ResponseFactoryInterface $responseFactory,
        private ProblemDetailsResponseFactory $problemFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        if (! array_key_exists('resource', $queryParams)) {
            return $this->problemFactory->createResponse($request, 400, 'Missing "resource" in query string');
        }

        $matches  = [];
        $resource = $queryParams['resource'];
        if (! preg_match('/^acct:(?P<account>.*)$/', $resource, $matches)) {
            return $this->problemFactory->createResponse($request, 400, 'Unknown resource type');
        }

        $account = $this->accountMap->match($matches['account']);

        $response = $this->responseFactory
            ->createResponse($account->getStatus())
            ->withHeader('Content-Type', $account->getContentType());
        $response->getBody()->write($account->getContent());

        return $response;
    }
}
