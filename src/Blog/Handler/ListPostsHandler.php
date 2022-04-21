<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact


declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\App\PaginationPreparation;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_merge;
use function count;
use function iterator_to_array;
use function sprintf;
use function str_replace;

class ListPostsHandler implements RequestHandlerInterface
{
    public function __construct(
        private MapperInterface $mapper,
        private TemplateRendererInterface $template,
        private RouterInterface $router,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tag   = str_replace(['+', '%20'], ' ', $request->getAttribute('tag', ''));
        $path  = $request->getAttribute('originalRequest', $request)->getUri()->getPath();
        $page  = PaginationPreparation::getPageFromRequest($request);
        $posts = $tag ? $this->mapper->fetchAllByTag($tag) : $this->mapper->fetchAll();

        $posts->setItemCountPerPage(10);
        $posts->setPageRange(7);

        // If the requested page is later than the last, redirect to the last
        if (count($posts) && $page > count($posts)) {
            return new RedirectResponse(sprintf('%s?page=%d', $path, count($posts)));
        }

        $posts->setCurrentPageNumber($page);

        return new HtmlResponse($this->template->render(
            'blog::list',
            $this->prepareView(
                $tag,
                iterator_to_array($posts->getItemsByPage($page)),
                PaginationPreparation::prepare($path, $page, $posts->getPages()),
            )
        ));
    }

    /**
     * @param BlogPost[] $entries
     */
    private function prepareView(string $tag, array $entries, object $pagination): array
    {
        $view = $tag ? ['tag' => $tag] : [];
        if ($tag) {
            $view['atom'] = $this->router->generateUri('blog.tag.feed', ['tag' => $tag, 'type' => 'atom']);
            $view['rss']  = $this->router->generateUri('blog.tag.feed', ['tag' => $tag, 'type' => 'rss']);
        }

        return array_merge($view, [
            'title'      => $tag ? 'Tag: ' . $tag : 'Blog Posts',
            'posts'      => $entries,
            'pagination' => $pagination,
        ]);
    }
}
