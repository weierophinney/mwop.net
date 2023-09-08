<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class AdminRebuildHandler implements RequestHandlerInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private TemplateRendererInterface $renderer,
    ) {
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $target = $request->getQueryParams()['target'] ?? null;

        return match ($target) {
            'blog'     => $this->rebuildBlog(),
            'homepage' => $this->rebuildHomepage(),
            default    => $this->unrecognizedTarget(),
        };

        // Do some work...
        // Render and return a response:
        return new HtmlResponse($this->renderer->render(
            'mwop::admin-rebuild',
            [] // parameters to pass to template
        ));
    }

    private function rebuildBlog(): ResponseInterface
    {
    }

    private function rebuildHomepage(): ResponseInterface
    {
    }

    private function unrecognizedTarget(): ResponseInterface
    {
    }
}
