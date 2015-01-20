<?php
namespace Mwop;

class ComicsPage extends Page
{
    public function __invoke($request, $response, $next)
    {
        error_log('In ' . get_class($this));
        if (! $request->getAttribute('user', false)) {
            error_log('Returning 401 error');
            return $next(401, $response->withStatus(401));
        }

        error_log('Rendering page');
        return parent::__invoke($request, $response, $next);
    }
}
