<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\Art\PhotoMapper;
use Psr\Http\Message\ResponseFactoryInterface;

class PhotosHandler implements RequestHandlerInterface
{
    public function __construct(
        private PhotoMapper $mapper,
        private int $perPage,
        private ResponseFactoryInterface $responseFactory,
        private TemplateRendererInterface $renderer,
    ) {
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $photos = $this->mapper->fetchAll();
        $photos->setItemCountPerPage($perPage);
        $photos->setCurrentPageNumber($request->getQueryParams()['page'] ?? 1);

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(
            $this->renderer->render('art::photos', [
                'photos' => $photos,
            ])
        );

        return $response;
    }
}
