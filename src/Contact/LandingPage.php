<?php
namespace Mwop\Contact;

use Aura\Session\Session;
use Phly\Mustache\Mustache;

class LandingPage
{
    private $config;
    private $page;
    private $path;
    private $renderer;
    private $session;

    public function __construct(Mustache $renderer, $path, $page, Session $session, array $config)
    {
        $this->renderer = $renderer;
        $this->path     = $path;
        $this->page     = $page;
        $this->session  = $session;
        $this->config   = $config;
    }

    public function __invoke($request, $response, $next)
    {
        if ($request->getUrl()->path !== $this->path) {
            return $next();
        }

        if ($request->getMethod() !== 'GET') {
            $response->setStatusCode(405);
            return $next('GET');
        }

        $view = array_merge($this->config, [
            'action' => rtrim($request->originalUrl, '/') . '/process',
            'csrf'   => $this->session->getCsrfToken()->getValue(),
        ]);

        $response->end($this->renderer->render($this->page, $view));
    }
}
