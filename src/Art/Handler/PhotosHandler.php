<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Mwop\Art\PhotoMapper;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PhotosHandler implements RequestHandlerInterface
{
    public function __construct(
        private PhotoMapper $mapper,
        private int $perPage,
        private ResponseFactoryInterface $responseFactory,
        private TemplateRendererInterface $renderer,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $page   = $request->getQueryParams()['page'] ?? 1;
        $photos = $this->mapper->fetchAll();
        $photos->setItemCountPerPage($this->perPage);
        $photos->setCurrentPageNumber($page);

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(
            $this->renderer->render('art::photos', [
                'page'   => $page,
                'photos' => $photos,
            ])
        );

        // Clear out statements from memory
        $photos = null;

        return $response;
    }
}
