<?php
namespace Mwop;

class ComicsPage extends Page
{
    public function __invoke($request, $response, $next)
    {
        if (! $request->user) {
            $response->setStatusCode(401);
            $next(401);
        }

        return parent::__invoke($request, $response, $next);
    }
}
