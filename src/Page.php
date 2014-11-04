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
        if (parse_url($request->originalUrl, PHP_URL_PATH) !== $this->path) {
            return $next();
        }

        if ($request->getMethod() !== 'GET') {
            $response->setStatus(405);
            return $next('GET');
        }

        $request->view->template = $this->page;
        $next();
    }
}
