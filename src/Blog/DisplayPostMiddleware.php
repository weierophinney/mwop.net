<?php
namespace Mwop\Blog;

use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use Mni\FrontYAML\Parser;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class DisplayPostMiddleware
{
    private $disqus;

    private $mapper;

    /**
     * Delimiter between post summary and extended body
     *
     * @var string
     */
    private $postDelimiter = '<!--- EXTENDED -->';

    private $template;

    public function __construct(MapperInterface $mapper, TemplateRendererInterface $template, array $disqus = [])
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

        $parser   = new Parser(null, new CommonMarkParser());
        $document = $parser->parse(file_get_contents($post['path']));
        $post     = $document->getYAML();
        $parts    = explode($this->postDelimiter, $document->getContent(), 2);
        $post     = array_merge($post, [
            'body'     => $parts[0],
            'extended' => isset($parts[1]) ? $parts[1] : '',
        ]);

        $original = $req->getOriginalRequest()->getUri()->getPath();
        $path     = substr($original, 0, -(strlen($post['id'] . '.html') + 1));
        $post     = new EntryView($post, $path, $this->disqus);

        return new HtmlResponse(
            $this->template->render('blog::post', $post)
        );
    }
}
