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
        $path = parse_url($request->getUrl(), PHP_URL_PATH);
        if ($path !== $this->path) {
            return $next();
        }

        if ($request->getMethod() !== 'GET') {
            $response->setStatusCode(405);
            return $next('GET');
        }

        $parentUrl = str_replace('/thank-you', '', $request->originalUrl);
        if (! $request->hasHeader('Referer')
            || ! preg_match('#^' . $parentUrl . '#', $request->getHeader('Referer'))
        ) {
            $response->setStatusCode(302);
            $response->addHeader('Location', $parentUrl);
            $response->end();
            return;
        }

        $request->view = (object) [
            'template' => $this->page,
            'model'    => [],
        ];
        $next();
    }
}
