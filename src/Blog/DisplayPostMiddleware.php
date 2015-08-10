<?php
namespace Mwop\Blog;

use Mwop\Template\TemplateInterface;
use Zend\Diactoros\Response\HtmlResponse;

class DisplayPostMiddleware
{
    private $disqus;

    private $mapper;

    private $template;

    public function __construct(MapperInterface $mapper, TemplateInterface $template, array $disqus = [])
    {
        $this->mapper   = $mapper;
        $this->template = $template;
        $this->disqus   = $disqus;
    }

    public function __invoke($req, $res, $next)
    {
        $post = $this->mapper->fetch($req->getAttribute('id', false));
        
        if (! $post) {
            return $next($req, $res->withStatus(404), 'Not found');
        }

        $post = include $post['path'];
        if (! $post instanceof EntryEntity) {
            return $next($req, $res->withStatus(404), 'Not found');
        }

        $original = $req->getOriginalRequest()->getUri()->getPath();
        $path     = substr($original, 0, -(strlen($post->getId() . '.html') + 1));
        $post     = new EntryView($post->getArrayCopy(), $path, $this->disqus);

        return new HtmlResponse(
            $this->template->render('blog.post', $post)
        );
    }
}
