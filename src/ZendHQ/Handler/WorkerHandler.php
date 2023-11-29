<?php

declare(strict_types=1);

namespace Mwop\ZendHQ\Handler;

use Mwop\ZendHQ\EventFactory;
use Mwop\ZendHQ\JobValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class WorkerHandler implements RequestHandlerInterface
{

    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $payload = json_decode(
            json: $request->getBody()->getContents(),
            associative: false,
            flags: JSON_THROW_ON_ERROR,
        );

        (new JobValidator())($payload);
        $event = (new EventFactory())($payload);

        $this->dispatcher->dispatch($event);

        return $this->responseFactory->createResponse(201);
    }
}
