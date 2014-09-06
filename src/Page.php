<?php
namespace Mwop;

use Phly\Mustache\Mustache;

class Page
{
    private $page;
    private $path;
    private $renderer;

    public function __construct(Mustache $renderer, $path, $page)
    {
        $this->renderer = $renderer;
        $this->path = $path;
        $this->page = $page;
    }

    public function __invoke($request, $response, $next)
    {
        if ($request->originalUrl->path !== $this->path) {
            return $next();
        }

        if ($request->getMethod() !== 'GET') {
            $response->setStatusCode(405);
            return $next('GET');
        }

        $response->end($this->renderer->render($this->page, []));
    }
}
