<?php
namespace Mwop;

use Phly\Mustache\Mustache;
use stdClass;

class Page
{
    private $page;
    private $path;

    public function __construct($path, $page)
    {
        $this->path = $path;
        $this->page = $page;
    }

    public function __invoke($request, $response, $next)
    {
        if ($request->getOriginalRequest()->getUri()->getPath() !== $this->path) {
            return $next();
        }

        if ($request->getMethod() !== 'GET') {
            return $next('GET', $response->withStatus(405));
        }

        $view = $request->getAttribute('view', new stdClass);
        $view->template = $this->page;
        $next($request->withAttribute('view', $view));
    }
}
