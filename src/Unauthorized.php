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
        error_log('In ' . get_class($this));
        if ($res->getStatusCode() !== 401) {
            error_log('Invalid status code; passing to next handler');
            return $next($err);
        }

        $new = $req->getUri()->withPath('/auth');
        $view = [
            'auth_path' => (string) $new,
            'redirect'  => (string) $req->getOriginalRequest()->getUri(),
        ];

        error_log('Rendering 401 response');
        return $res->end($this->renderer->render($this->template, $view));
    }
}
