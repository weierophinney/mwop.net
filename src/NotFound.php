<?php
namespace Mwop;

class NotFound
{
    public function __invoke($req, $res, $next)
    {
        return $next('Not Found', $res->withStatus(404));
    }
}
