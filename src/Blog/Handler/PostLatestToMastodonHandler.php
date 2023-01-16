<?php

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Mwop\Blog\Mastodon\PostLatestEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PostLatestToMastodonHandler implements RequestHandlerInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->dispatcher->dispatch(new PostLatestEvent());
        return $this->responseFactory->createResponse(204);
    }
}
