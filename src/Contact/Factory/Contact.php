<?php
namespace Mwop\Contact\Factory;

use Mwop\Contact\Middleware;

class Contact
{
    public function __invoke($services)
    {
        return new Middleware(
            $services->get('contact.landing'),
            function ($req, $res, $next) {
                if ($req->getMethod() !== 'POST') {
                    $res->setStatusCode(405);
                    return $next('POST');
                }
                $res->setStatusCode(302);
                $path = str_replace('/process', '', $res->originalUrl);
                $res->addHeader('Location', $path);
                $res->end();
            },
            $services->get('contact.thankyou')
        );
    }
}
