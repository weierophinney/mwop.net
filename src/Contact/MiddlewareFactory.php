<?php
namespace Mwop\Contact;

use Zend\Expressive\AppFactory;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        $contact = AppFactory::create($services);

        $contact->get('/', LandingPage::class);
        $contact->post('/process', Process::class);
        $contact->get('/thank-you', ThankYouPage::class);
        $contact->pipe(function ($req, $res, $next) use ($services) {
            if ('GET' !== strtoupper($req->getMethod())) {
                return $next(
                    $req,
                    $res
                        ->withStatus(405)
                        ->withHeader('Allow', 'GET'),
                    405
                );
            }
            $middleware = $services->get(LandingPage::class);
            return $middleware($req, $res, $next);
        });

        return $contact;
    }
}
