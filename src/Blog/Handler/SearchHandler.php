<?php

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Mwop\Blog\Mapper\MapperInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;

class SearchHandler implements RequestHandlerInterface
{
    private $mapper;
    private $urlHelper;

    public function __construct(MapperInterface $mapper, UrlHelper $urlHelper)
    {
        $this->mapper = $mapper;
        $this->urlHelper = $urlHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $toMatch = $request->getQueryParams()['q'] ?? '';

        if ('' === $toMatch) {
            return new JsonResponse([]);
        }

        $results = array_map(function ($row) {
            return [
                'link' => $this->urlHelper->generate('blog.post', ['id' => $row['id']]),
                'title' => $row['title'],
            ];
        }, $this->mapper->search($toMatch));

        return new JsonResponse($results);
    }
}
