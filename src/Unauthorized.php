<?php
namespace Mwop;

use Phly\Http\Uri;
use Phly\Mustache\Mustache;

class Unauthorized
{
    private $renderer;
    private $template;

    public function __construct(Mustache $renderer, $template = 'error/401')
    {
        $this->renderer = $renderer;
        $this->template = $template;
    }

    public function __invoke($err, $req, $res, $next)
    {
        if ($res->getStatusCode() !== 401) {
            return $next($err);
        }

        $url = new Uri($req->getUrl());
        $new = $url->setPath('/auth');
        $view = [
            'auth_path' => (string) $new,
            'redirect'  => $req->originalUrl,
        ];

        $res->end($this->renderer->render($this->template, $view));
    }
}
