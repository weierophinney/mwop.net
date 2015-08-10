<?php
namespace Mwop;

use Zend\Diactoros\Response\HtmlResponse;

class Page
{
    private $page;
    private $template;
    private $viewModel;

    public function __construct($page, $viewModel, Template\TemplateInterface $template)
    {
        $this->page      = $page;
        $this->viewModel = is_array($viewModel) ? (object) $viewModel : $viewModel;
        $this->template  = $template;
    }

    public function __invoke($request, $response, $next)
    {
        return new HtmlResponse(
            $this->template->render($this->page, $this->viewModel)
        );
    }
}
