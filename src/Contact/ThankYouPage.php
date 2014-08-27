<?php
namespace Mwop\Contact;

use Phly\Mustache\Mustache;

class ThankYouPage
{
    private $page;
    private $path;
    private $renderer;

    public function __construct(Mustache $renderer, $path, $page)
    {
        $this->renderer = $renderer;
        $this->path     = $path;
        $this->page     = $page;
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

        $parentUrl = str_replace('/thank-you', '', $request->originalUrl);
        if (! $request->hasHeader('Referer')
            || ! preg_match('#^' . $parentUrl . '#', $request->getHeader('Referer'))
        ) {
            $response->setStatusCode(302);
            $response->addHeader('Location', $parentUrl);
            $response->end();
            return;
        }

        $response->end($this->renderer->render($this->page, []));
    }
}
