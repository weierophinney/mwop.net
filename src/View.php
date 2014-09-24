<?php
namespace Mwop;

use Phly\Mustache\Mustache;

class View
{
    private $renderer;

    public function __construct(Mustache $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response, $next)
    {
        if ($response->isComplete()) {
            return $next();
        }

        if (! $request->view || ! $request->view->template) {
            return $next();
        }

        $response->write($this->renderer->render(
            $request->view->template,
            $request->view->model
        ));
    }
}
