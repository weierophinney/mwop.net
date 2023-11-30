<?php

declare(strict_types=1);

namespace Mwop\ZendHQ\Middleware;

use Mezzio\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

use function strtolower;

class ContentTypeMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = strtolower($request->getHeaderLine('Content-Type'));

        if ($contentType !== 'application/mwop-net-jq+json') {
            throw new class extends RuntimeException implements ProblemDetailsExceptionInterface {
                use CommonProblemDetailsExceptionTrait;

                public function __construct()
                {
                    $status  = 415;
                    $message = 'Invalid payload Content-Type provided';

                    parent::__construct($message, $status);

                    $this->status     = $status;
                    $this->detail     = $message;
                    $this->title      = 'Unsupported Media Type';
                    $this->type       = 'https://httpstatuses.io/415';
                    $this->additional = [
                        'expected' => 'application/mwop-net-jq+json',
                    ];
                }
            };
        }

        return $handler->handle($request);
    }
}
