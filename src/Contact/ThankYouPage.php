<?php
namespace Mwop\Contact;

class ThankYouPage
{
    private $page;
    private $path;

    public function __construct($path, $page)
    {
        $this->path     = $path;
        $this->page     = $page;
    }

    public function __invoke($request, $response, $next)
    {
        $path = $request->getUri()->getPath();
        if ($path !== $this->path) {
            return $next($request, $response);
        }

        if ($request->getMethod() !== 'GET') {
            return $next($request, $response->withStatus(405), 'GET');
        }

        $parentUrl = str_replace('/thank-you', '', $request->originalUrl);
        if (! $request->hasHeader('Referer')
            || ! preg_match('#^' . $parentUrl . '#', $request->getHeader('Referer'))
        ) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', $parentUrl)
                ->end();
        }

        return $next($request->withAttribute('view', (object) [
            'template' => $this->page,
            'model'    => [],
        ]), $response);
    }
}
