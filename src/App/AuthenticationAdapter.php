<?php

declare(strict_types=1);

namespace Mwop\App;

use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\Session\PhpSession;
use Mezzio\Authentication\UserInterface;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationAdapter implements AuthenticationInterface
{
    public const REDIRECT_ATTRIBUTE = 'authentication:redirect';

    public function __construct(private readonly PhpSession $implementation)
    {
    }

    public function authenticate(ServerRequestInterface $request): ?UserInterface
    {
        return $this->implementation->authenticate($request);
    }

    public function unauthorizedResponse(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if ($session instanceof SessionInterface) {
            $session->set(self::REDIRECT_ATTRIBUTE, (string) $request->getUri());
        }
        return $this->implementation->unauthorizedResponse($request);
    }
}
