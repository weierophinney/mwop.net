<?php

declare(strict_types=1);

namespace Mwop\Hooks\Middleware;

use Mezzio\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class ValidateWebhookRequestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private string $expectedHeader,
        private string $expectedToken,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->expectedToken !== $request->getHeaderLine($this->expectedHeader)) {
            $this->raiseInvalidRequestException();
        }

        return $handler->handle($request);
    }

    private function raiseInvalidRequestException(): void
    {
        throw new class () extends RuntimeException implements ProblemDetailsExceptionInterface {
            use CommonProblemDetailsExceptionTrait;

            public function __construct()
            {
                parent::__construct('Invalid client token', 400);
                $this->status = 400;
                $this->title  = 'Invalid client token';
                $this->type   = 'net.mwop.api.invalid-token';
                $this->detail = 'The token was either missing or invalid.';
            }
        };
    }
}
