<?php
namespace Mwop;

class ComicsPage extends Page
{
    public function __invoke($request, $response, $next)
    {
        /*
        if (! $request->getAttribute('user', false)) {
            return $next($request, $response->withStatus(401), 401);
        }
         */

        return parent::__invoke($request, $response, $next);
    }
}
