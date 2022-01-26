<?php

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use InvalidArgumentException;
use Mezzio\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;
use Mwop\Blog\Twitter\TweetPostEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_string;

class TweetPostHandler implements RequestHandlerInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getParsedBody()['id'] ?? null;
        if (null === $id || ! is_string($id) || '' === $id) {
            throw new class extends InvalidArgumentException implements ProblemDetailsExceptionInterface {
                use CommonProblemDetailsExceptionTrait;

                public function __construct()
                {
                    parent::__construct('Missing Post ID', 400);
                    $this->status = 400;
                    $this->type   = 'net.mwop.blog.missing--post-id';
                    $this->title  = 'Missing Post ID';
                    $this->detail = 'The request was missing a post identifier, or it was not a non-empty-string.';
                }
            };
        }

        $this->dispatcher->dispatch(new TweetPostEvent($id));
        return $this->responseFactory->createResponse(204);
    }
}
