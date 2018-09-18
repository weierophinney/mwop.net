<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use DateTimeImmutable;
use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use Mni\FrontYAML\Parser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

use function date;

class DisplayPostHandler implements RequestHandlerInterface
{
    private $disqus;

    private $mapper;

    /**
     * @var RequestHandlerInterface
     */
    private $notFoundHandler;

    /**
     * Delimiter between post summary and extended body
     *
     * @var string
     */
    private $postDelimiter = '<!--- EXTENDED -->';

    private $template;

    public function __construct(
        MapperInterface $mapper,
        TemplateRendererInterface $template,
        RequestHandlerInterface $notFoundHandler,
        array $disqus = []
    ) {
        $this->mapper          = $mapper;
        $this->template        = $template;
        $this->notFoundHandler = $notFoundHandler;
        $this->disqus          = $disqus;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $post = $this->mapper->fetch($request->getAttribute('id', false));

        if (! $post) {
            return $this->notFoundHandler->handle($request);
        }

        $isAmp = (bool) ($request->getQueryParams()['amp'] ?? false);

        $parser   = new Parser(null, new CommonMarkParser());
        $document = $parser->parse(file_get_contents($post['path']));
        $post     = $document->getYAML();
        $parts    = explode($this->postDelimiter, $document->getContent(), 2);
        $post     = array_merge($post, [
            'body'      => $parts[0],
            'extended'  => isset($parts[1]) ? $parts[1] : '',
            'updated'   => $post['updated'] && $post['updated'] !== $post['created'] ? $post['updated'] : false,
            'tags'      => is_array($post['tags']) ? $post['tags'] : explode('|', trim((string) $post['tags'], '|')),
        ]);

        $lastModified = new DateTimeImmutable($post['updated'] ?: $post['created']);

        return new HtmlResponse(
            $this->template->render(
                $isAmp ? 'blog::post.amp' : 'blog::post',
                [
                    'post' => $post,
                    'disqus' => $this->disqus,
                ]
            ),
            200,
            [
                'Last-Modified' => $lastModified->format('r'),
            ]
        );
    }
}
