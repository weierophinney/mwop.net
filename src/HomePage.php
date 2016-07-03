<?php
namespace Mwop;

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePage
{
    const TEMPLATE = 'mwop::home.page';

    private $mapper;
    private $renderer;
    private $router;

    public function __construct(
        Blog\MapperInterface $mapper,
        RouterInterface $router,
        TemplateRendererInterface $renderer
    ) {
        $this->mapper   = $mapper;
        $this->router   = $router;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response, $next)
    {
        return new HtmlResponse(
            $this->renderer->render(self::TEMPLATE, [
                'posts' => $this->fetchPosts(),
            ])
        );
    }

    private function fetchPosts()
    {
        $posts = $this->mapper->fetchAll();
        $posts->setItemCountPerPage(5);

        return array_map(function ($post) {
            return [
                'title' => $post['title'],
                'url'   => $this->router->generateUri('blog.post', ['id' => $post['id']]),
            ];
        }, iterator_to_array($posts->getItemsByPage(1)));
    }
}
