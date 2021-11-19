<?php

declare(strict_types=1);

namespace Mwop\Blog\Middleware;

use InvalidArgumentException;
use Mezzio\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidateAPIKeyMiddleware implements MiddlewareInterface
{
    public function __construct(
        private string $apiKey,
        private string $tokenHeader,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $apiKey = $request->getHeaderLine($this->tokenHeader);
        if (empty($apiKey) || $apiKey !== $this->apiKey) {
            throw new class extends InvalidArgumentException implements ProblemDetailsExceptionInterface {
                use CommonProblemDetailsExceptionTrait;

                public function __construct()
                {
                    parent::__construct('Unauthorized', 403);
                    $this->status = 403;
                    $this->type   = 'net.mwop.blog.unauthorized';
                    $this->title  = 'Unauthorized';
                    $this->detail = 'You are not authorized to perform the requested action';
                }
            };
        }

        return $handler->handle($request);
    }
}
