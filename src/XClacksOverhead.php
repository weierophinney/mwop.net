<?php
namespace Mwop;

class XClacksOverhead
{
    public function __invoke($req, $res, $next)
    {
        $res = $next($req, $res);
        return $res->withHeader('X-Clacks-Overhead', 'GNU Terry Pratchett');
    }
}
