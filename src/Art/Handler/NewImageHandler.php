<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Mwop\Art\Webhook\Payload;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class NewImageHandler implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private EventDispatcherInterface $dispatcher,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $content = trim($request->getBody()->__toString());

        $this->logger->info(sprintf('Received Instagram payload: %s', $content));
        $this->dispatcher->dispatch(new Payload($content));

        return $this->responseFactory->createResponse(204);
    }
}
