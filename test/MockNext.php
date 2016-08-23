<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MockNext
{
    public function __invoke(Request $request, Response $response) : Response
    {
        return $this->next($request, $response);
    }

    public function next(Request $request, Response $response) : Response
    {
    }
}
