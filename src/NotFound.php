<?php
namespace Mwop;

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class NotFound
{
    private $page;
    private $template;
    private $viewModel;

    public function __construct(
        TemplateRendererInterface $template,
        RouterInterface $router,
        $page = 'error::404'
    ) {
        $this->template  = $template;
        $this->viewModel = new PageView();
        $this->viewModel->setRouter($router);
        $this->page      = $page;
    }

    public function __invoke($request, $response, $next)
    {
        return new HtmlResponse(
            $this->template->render($this->page, $this->viewModel),
            404
        );
    }
}
