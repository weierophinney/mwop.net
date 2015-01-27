<?php
namespace Mwop;

class NotFound
{
    public function __invoke($req, $res, $next)
    {
        return $next($req, $res->withStatus(404), 'Not Found');
    }
}
