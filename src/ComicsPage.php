<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

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
