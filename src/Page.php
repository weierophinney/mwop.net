<?php
namespace Mwop;

use Phly\Mustache\Mustache;

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
        if ($request->originalUrl->path !== $this->path) {
            return $next();
        }

        if ($request->getMethod() !== 'GET') {
            $response->setStatusCode(405);
            return $next('GET');
        }

        $request->view->template = $this->page;
error_log(sprintf('Set template to %s', $this->page));
        $next();
    }
}
