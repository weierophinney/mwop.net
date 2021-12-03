<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact


declare(strict_types=1);

namespace Mwop\Blog\Handler;

use ArrayAccess;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Helper\UrlHelper;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_map;

class SearchHandler implements RequestHandlerInterface
{
    public function __construct(
        private MapperInterface $mapper,
        private UrlHelper $urlHelper,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $toMatch = $request->getQueryParams()['q'] ?? '';

        if ('' === $toMatch) {
            return new JsonResponse([]);
        }

        // phpcs:ignore
        $results = array_map(function (array|ArrayAccess $row): array {
            return [
                'link'  => $this->urlHelper->generate('blog.post', ['id' => $row['id']]),
                'title' => $row['title'],
            ];
        }, $this->mapper->search($toMatch));

        return new JsonResponse($results);
    }
}
