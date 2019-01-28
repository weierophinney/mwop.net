<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Mwop\Blog\FetchBlogPostEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

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

        // @var \Mwop\Blog\FetchBlogPostEvent $event
        $event = $this->dispatcher->dispatch(new FetchBlogPostEvent($id));

        // @var null|\Mwop\Blog\BlogPost $post
        $post = $event->blogPost();

        if (! $post) {
            return $this->notFoundHandler->handle($request);
        }

        // @var \DateTimeInterface $lastModified
        $lastModified = $post->updated ?: $post->created;

        return new HtmlResponse(
            $this->template->render('blog::post', [
                'post'   => $post,
                'disqus' => $this->disqus,
            ]),
            200,
            [
                'Last-Modified' => $lastModified->format('r'),
            ]
        );
    }
}
