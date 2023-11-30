<?php

declare(strict_types=1);

namespace Mwop\ZendHQ\Middleware;

use Mezzio\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class HostNameMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $host = $request->getHeaderLine('Host');
        if ($host !== 'nginx') {
            throw new class extends RuntimeException implements ProblemDetailsExceptionInterface {
                use CommonProblemDetailsExceptionTrait;

                public function __construct()
                {
                    $status  = 421;
                    $message = 'Unable to honor this request via the requested host';

                    parent::__construct($message, $status);

                    $this->status     = $status;
                    $this->detail     = $message;
                    $this->title      = 'Misdirected Request';
                    $this->type       = 'https://httpstatuses.io/421';
                }
            };
        }

        return $handler->handle($request);
    }
}
