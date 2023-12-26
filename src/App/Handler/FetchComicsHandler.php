<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\App\EventDispatcher\DeferredEvent;
use Mwop\Comics\ComicsEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class FetchComicsHandler implements RequestHandlerInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private TemplateRendererInterface $renderer,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $this->dispatcher->dispatch(new DeferredEvent(new ComicsEvent()));

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html');
        $response->getBody()->write($this->renderer->render(
            'mwop::admin/fetch-comics',
            [] // parameters to pass to template
        ));

        return $response;
    }
}
