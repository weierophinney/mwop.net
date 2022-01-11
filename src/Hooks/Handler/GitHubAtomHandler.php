<?php

declare(strict_types=1);

namespace Mwop\Hooks\Handler;

use Mwop\Hooks\WebhookPayload;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GitHubAtomHandler implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->dispatcher->dispatch(new WebhookPayload((string) $request->getBody()));

        return $this->responseFactory->createResponse(204);
    }
}
