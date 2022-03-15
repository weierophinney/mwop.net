<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact


declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Mwop\Blog\BlogPost;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DisplayPostHandler implements RequestHandlerInterface
{
    public function __construct(
        private MapperInterface $mapper,
        private TemplateRendererInterface $template,
        private ResponseFactoryInterface $responseFactory,
        private RequestHandlerInterface $notFoundHandler,
        private array $disqus = [],
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id', false);

        if (! $id) {
            return $this->notFoundHandler->handle($request);
        }

        $post = $this->mapper->fetch($id);

        if (! $post instanceof BlogPost) {
            return $this->notFoundHandler->handle($request);
        }

        $lastModified = $post->updated ?: $post->created;

        $response = $this->responseFactory->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withHeader('Last-Modified', $lastModified->format('r'));

        $response->getBody()->write(
            $this->template->render('blog::post', [
                'post'   => $post,
                'disqus' => $this->disqus,
            ]),
        );

        return $response;
    }
}
