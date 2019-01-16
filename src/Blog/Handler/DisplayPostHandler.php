<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Handler;

use DateTimeImmutable;
use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use Mni\FrontYAML\Parser;
use Mwop\Blog\BlogPostEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

use function date;

class DisplayPostHandler implements RequestHandlerInterface
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var array<string, string> */
    private $disqus;

    /** @var RequestHandlerInterface */
    private $notFoundHandler;

    /** @var TemplateRendererInterface */
    private $template;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TemplateRendererInterface $template,
        RequestHandlerInterface $notFoundHandler,
        array $disqus = []
    ) {
        $this->dispatcher      = $dispatcher;
        $this->template        = $template;
        $this->notFoundHandler = $notFoundHandler;
        $this->disqus          = $disqus;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $id = $request->getAttribute('id', false);

        if (! $id) {
            return $this->notFoundHandler->handle($request);
        }

        // @var \Mwop\Blog\BlogPostEvent $event
        $event = $this->dispatcher->dispatch(new BlogPostEvent($id));

        // @var null|\Mwop\Blog\BlogPost $post
        $post = $event->blogPost();

        if (! $post) {
            return $this->notFoundHandler->handle($request);
        }

        // @var \DateTimeInterface $lastModified
        $lastModified = $post->updated ?: $post->created;
        $isAmp        = (bool) ($request->getQueryParams()['amp'] ?? false);

        return new HtmlResponse(
            $this->template->render(
                $isAmp ? 'blog::post.amp' : 'blog::post',
                [
                    'post'   => $post,
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
