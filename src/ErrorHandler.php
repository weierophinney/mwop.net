<?php
namespace Mwop;

use Exception;
use Phly\Mustache\Mustache;

class ErrorHandler
{
    private $displayErrors;

    private $renderer;

    public function __construct(Mustache $renderer, $displayErrors = false)
    {
        $this->renderer = $renderer;
        $this->displayErrors = (bool) $displayErrors;
    }

    public function __invoke($err, $req, $res, $next)
    {
        if ($res->getStatusCode() === 404) {
            $res->end($this->renderer->render('error/404', []));
            return;
        }

        if ($res->getStatusCode() < 400) {
            $res->setStatus(500);
        }

        $error = $err;

        if (is_array($err)) {
            $error = json_encode($err, JSON_PRETTY_PRINT);
        }

        if ($err instanceof Exception) {
            $error = $err->getTraceAsString();
        }

        if (is_object($err)) {
            $error = (string) $err;
        }

        $res->end($this->renderer->render('error/500', [
            'error' => $this->displayErrors ? ['error' => $err] : false,
        ]));
    }
}
