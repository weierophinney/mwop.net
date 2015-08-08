<?php
namespace Mwop;

class ComicsPage extends Page
{
    public function __invoke($request, $response, $next)
    {
        error_log(sprintf("In %s", __CLASS__));
        if (! $request->getAttribute('user', false)) {
            error_log('No user present; calling next with error');
            return $next($request, $response->withStatus(401), 401);
        }

        error_log('User found; returning page');
        return parent::__invoke($request, $response, $next);
    }
}
