<?php
namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ComicsPage extends Page
{
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if (! $request->getAttribute('user', false)) {
            return $next($request, $response->withStatus(401), 401);
        }

        return parent::__invoke($request, $response, $next);
    }
}
