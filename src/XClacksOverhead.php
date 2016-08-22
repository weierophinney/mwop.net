<?php
namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class XClacksOverhead
{
    public function __invoke(Request $req, Response $res, callable $next) : Response
    {
        $res = $next($req, $res);
        return $res->withHeader('X-Clacks-Overhead', 'GNU Terry Pratchett');
    }
}
