<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\OAuth2;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait RenderUnauthorizedResponseTrait
{
    private bool $isDebug = false;

    private TemplateRendererInterface $renderer;

    private ResponseFactoryInterface $responseFactory;

    private function renderUnauthorizedResponse(
        ServerRequestInterface $request,
        ?string $redirect = null,
        ?string $error = null
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse(401, 'Unauthorized');

        $response->getBody()->write($this->renderer->render('oauth2::401', [
            'auth_path' => '/auth',
            'redirect'  => $redirect ?? $request->getUri(),
            'debug'     => $this->isDebug,
            'error'     => $error,
        ]));

        return $response;
    }
}
