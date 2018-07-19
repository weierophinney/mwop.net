<?php

declare(strict_types=1);

namespace Mwop;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SetHostNameMiddleware implements MiddlewareInterface
{
    /**
     * Determine if the host matches the production values, which are behind a
     * reverse proxy, and, if so, reset them.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $uri = $request->getUri();
        if ($uri->getHost() === 'nginx' && $uri->getPort() == 8080) {
            $uri = $uri
                ->withHost('mwop.net')
                ->withPort(80);
            $request = $request->withUri($uri);
        }

        return $handler->handle($request);
    }
}
