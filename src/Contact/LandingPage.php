<?php
namespace Mwop\Contact;

use Aura\Session\Session;

class LandingPage
{
    private $config;
    private $page;
    private $path;
    private $session;

    public function __construct($path, $page, Session $session, array $config)
    {
        $this->path     = $path;
        $this->page     = $page;
        $this->session  = $session;
        $this->config   = $config;
    }

    public function __invoke($request, $response, $next)
    {
        $path = $request->getUri()->getPath() ?: '/';
        if ($path !== $this->path) {
            return $next($request, $response);
        }

        if ($request->getMethod() !== 'GET') {
            return $next($request, $response->withStatus(405), 'GET');
        }

        $basePath = $request->getOriginalRequest()->getUri()->getPath();
        $view = array_merge($this->config, [
            'action' => rtrim($basePath, '/') . '/process',
            'csrf'   => $this->session->getCsrfToken()->getValue(),
        ]);

        return $next($request->withAttribute('view', (object) [
            'template' => $this->page,
            'model'    => $view,
        ]), $response);
    }
}
