<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class XPoweredBy
{
    public function __invoke(Request $req, Response $res, callable $next) : Response
    {
        $res = $next($req, $res);
        return $res->withHeader('X-Powered-By', 'Coffee, Beer, and Whiskey, in no particular order');
    }
}
