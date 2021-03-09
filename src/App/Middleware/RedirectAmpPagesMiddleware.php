<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function http_build_query;
use function ini_get;

use const PHP_QUERY_RFC3986;

class RedirectAmpPagesMiddleware implements MiddlewareInterface
{
    public function __construct(private ResponseFactoryInterface $responseFactory)
    {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $query = $request->getQueryParams();
        $isAmp = (bool) ($query['amp'] ?? false);

        if (! $isAmp) {
            return $handler->handle($request);
        }

        unset($query['amp']);

        $uri = $request->getUri()
            ->withQuery(http_build_query(
                $query,
                '',
                ini_get('arg_separator.output'),
                PHP_QUERY_RFC3986
            ));

        return $this->responseFactory->createResponse(301)
            ->withHeader('Location', (string) $uri)
            ->withHeader('X-Robots-Tag', 'noindex');
    }
}
