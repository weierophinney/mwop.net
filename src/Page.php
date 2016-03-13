<?php
namespace Mwop;

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class Page
{
    private $page;
    private $template;

    public function __construct($page, TemplateRendererInterface $template)
    {
        $this->page      = $page;
        $this->template  = $template;
    }

    public function __invoke($request, $response, $next)
    {
        return new HtmlResponse(
            $this->template->render($this->page, [])
        );
    }
}
