<?php
namespace Mwop;

class Unauthorized
{
    private $renderer;
    private $template;

    public function __construct(/* $renderer, $template = 'error/401' */)
    {
        /*
        $this->renderer = $renderer;
        $this->template = $template;
         */
    }

    public function __invoke($err, $req, $res, $next)
    {
        if ($res->getStatusCode() !== 401) {
            return $next($err);
        }

        /*
        $renderer->render($template);
         */
        $res->end();
    }
}
