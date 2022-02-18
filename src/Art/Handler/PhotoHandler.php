<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\Art\PhotoMapper;
use Psr\Http\Message\ResponseFactoryInterface;

class PhotoHandler implements RequestHandlerInterface
{
    public function __construct(
        private PhotoMapper $mapper,
        private ResponseFactoryInterface $responseFactory,
        private TemplateRendererInterface $renderer,
    ) {
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $photo = $this->mapper->fetch($request->getAttribute('photo'));

        if (null === $photo) {
            $response = $this->responseFactory->createResponse(404)
                ->withHeader('Content-Type', 'text/html');
            $response->getBody()->write($this->renderer->render('error::404'));

            return $response;
        }

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(
            $this->renderer->render('art::photo', [
                'photo' => $photo,
            ])
        );
    }
}
