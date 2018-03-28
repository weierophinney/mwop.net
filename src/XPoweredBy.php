<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class XPoweredBy implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler) : Response
    {
        $response = $handler->handle($request);
        return $response->withHeader('X-Powered-By', 'Coffee, Beer, and Whiskey, in no particular order');
    }
}
