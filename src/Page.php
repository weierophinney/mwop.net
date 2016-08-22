<?php
namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class Page
{
    private $page;
    private $template;

    public function __construct(string $page, TemplateRendererInterface $template)
    {
        $this->page      = $page;
        $this->template  = $template;
    }

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        return new HtmlResponse(
            $this->template->render($this->page, [])
        );
    }
}
