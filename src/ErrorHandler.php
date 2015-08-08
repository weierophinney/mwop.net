<?php
namespace Mwop;

use Exception;
use Zend\Diactoros\Response\HtmlResponse;

class ErrorHandler
{
    private $displayErrors;

    private $renderer;

    public function __construct(Template\TemplateInterface $renderer, $displayErrors = false)
    {
        $this->renderer      = $renderer;
        $this->displayErrors = (bool) $displayErrors;
    }

    public function __invoke($err, $req, $res, $next)
    {
        if ($res->getStatusCode() === 404) {
            return new HtmlResponse(
                $this->renderer->render('error/404'),
                404
            );
        }

        if ($res->getStatusCode() < 400) {
            $res = $res->withStatus(500);
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

        return new HtmlResponse(
            $this->renderer->render('error/500', [
                'error' => $this->displayErrors ? ['error' => $err] : false,
            ]),
            $res->getStatusCode()
        );
    }
}
