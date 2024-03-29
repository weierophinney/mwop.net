<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class LogoutHandler implements RequestHandlerInterface
{
    public function handle(Request $request): Response
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        if ($session->has(UserInterface::class)) {
            $session->unset(UserInterface::class);
        }

        return $this->redirect($request);
    }

    private function redirect(Request $request): RedirectResponse
    {
        $originalUri = $request->getAttribute('originalRequest', $request)?->getUri() ?: $request->getUri();
        $redirectUri = $originalUri->withPath('/');

        return new RedirectResponse((string) $redirectUri);
    }
}
