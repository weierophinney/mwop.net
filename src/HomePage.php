<?php
namespace Mwop;

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePage
{
    const TEMPLATE = 'mwop::home.page';

    private $posts;
    private $renderer;

    public function __construct(
        array $posts,
        TemplateRendererInterface $renderer
    ) {
        $this->posts    = $posts ;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response, $next)
    {
        return new HtmlResponse(
            $this->renderer->render(self::TEMPLATE, [
                'posts' => $this->posts,
            ])
        );
    }
}
