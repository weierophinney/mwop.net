<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Uri;
use Mezzio\Authentication\UserInterface;
use Mezzio\Session\SessionInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\App\AuthenticationAdapter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;

class LoginHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private AuthenticationAdapter $adapter
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $session  = $request->getAttribute('session');
        $redirect = $this->getRedirect($request, $session);

        // Handle submitted credentials
        if ('POST' === $request->getMethod()) {
            return $this->handleLoginAttempt($request, $session, $redirect);
        }

        if ($session->has(UserInterface::class)) {
            $session->unset(AuthenticationAdapter::REDIRECT_ATTRIBUTE);
            return new RedirectResponse($redirect);
        }

        // Display initial login form
        return new HtmlResponse($this->renderer->render(
            'mwop::login',
            []
        ));
    }

    private function getRedirect(
        ServerRequestInterface $request,
        SessionInterface $session
    ): string {
        $redirect = $session->get(AuthenticationAdapter::REDIRECT_ATTRIBUTE);

        if (! $redirect) {
            $redirect = new Uri($request->getHeaderLine('Referer'));
            if (in_array($redirect->getPath(), ['', '/login'], true)) {
                $redirect = '/';
            }
        }

        return $redirect;
    }

    private function handleLoginAttempt(
        ServerRequestInterface $request,
        SessionInterface $session,
        string $redirect
    ): ResponseInterface {
        // User session takes precedence over user/pass POST in
        // the auth adapter so we remove the session prior
        // to auth attempt
        $session->unset(UserInterface::class);

        // Login was successful
        if ($this->adapter->authenticate($request)) {
            $session->unset(AuthenticationAdapter::REDIRECT_ATTRIBUTE);
            return new RedirectResponse($redirect);
        }

        // Login failed
        return new HtmlResponse($this->renderer->render(
            'mwop::login',
            ['error' => 'Invalid credentials; please try again']
        ));
    }
}
