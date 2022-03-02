<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\Art\UploadPhoto;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProcessUploadHandler implements RequestHandlerInterface
{
    public function __construct(
        private UploadPhoto $uploader,
        private TemplateRendererInterface $renderer,
        private ResponseFactoryInterface $responseFactory,
        private UrlHelper $url,
    ) {
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $result = $this->uploader->process($request);

        if ($result->isError()) {
            $response = $this->responseFactory->createResponse()
                ->withHeader('Content-Type', 'text/html');
            $response->getBody()->write($this->renderer->render(
                'art::upload',
                [
                    'error' => $result->getError(),
                    'form'  => $request->getParsedBody(),
                ],
            ));

            return $response;
        }

        $response = $this->responseFactory->createResponse(302)
            ->withHeader('Location', $this->url->generate('art.photo', ['image' => $result->filename()]));

        return $response;
    }
}
