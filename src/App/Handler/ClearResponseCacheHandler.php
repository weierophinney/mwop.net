<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ClearResponseCacheHandler implements RequestHandlerInterface
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private ResponseFactoryInterface $responseFactory,
        private TemplateRendererInterface $renderer,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->cache->clear();

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html');

        $response->getBody()->write($this->renderer->render('mwop::admin/clear-response-cache', []));

        return $response;
    }
}
