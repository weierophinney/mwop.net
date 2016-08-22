<?php
namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class Unauthorized
{
    private $renderer;
    private $template;

    public function __construct(
        TemplateRendererInterface $renderer,
        string $template = 'error::401'
    ) {
        $this->renderer = $renderer;
        $this->template = $template;
    }

    public function __invoke($err, Request $req, Response $res, callable $next) : Response
    {
        if ($res->getStatusCode() !== 401) {
            return $next($req, $res, $err);
        }

        $new  = $req->getUri()->withPath('/auth');
        $view = [
            'auth_path' => (string) $new,
            'redirect'  => (string) $req->getOriginalRequest()->getUri(),
        ];

        return new HtmlResponse(
            $this->renderer->render($this->template, $view),
            401
        );
    }
}
