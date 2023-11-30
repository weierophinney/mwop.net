<?php

declare(strict_types=1);

namespace Mwop\ZendHQ\Handler;

use Mwop\ZendHQ\JobValidator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class WorkerHandler implements RequestHandlerInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $payload = json_decode(
            json: $request->getBody()->getContents(),
            associative: true,
            flags: JSON_THROW_ON_ERROR,
        );

        (new JobValidator())($payload);

        $class = $payload['type'];
        $event = $class::fromDataArray($payload['data'] ?? []);

        $this->dispatcher->dispatch($event);

        return $this->responseFactory->createResponse(201);
    }
}
