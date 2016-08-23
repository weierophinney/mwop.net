<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait HttpMessagesTrait
{
    public function createRequestMock()
    {
        return $this->prophesize(Request::class);
    }

    public function createResponseMock()
    {
        return $this->prophesize(Response::class);
    }

    public function createNextMock()
    {
        return $this->prophesize(MockNext::class);
    }
}
