<?php
namespace Mwop;

class NotFound
{
    public function __invoke($req, $res, $next)
    {
        $res->setStatusCode(404);
        $next('Not Found');
    }
}
