<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface as Uri;
use Zend\Stratigility\Http\Request as StratigilityRequest;

trait HttpMessagesTrait
{
    public function createRequestMock()
    {
        $request = $this->prophesize(StratigilityRequest::class);
        $request->willImplement(Request::class);
        return $request;
    }

    public function createResponseMock()
    {
        return $this->prophesize(Response::class);
    }

    public function nextShouldNotBeCalled()
    {
        return function ($request, $response, $error = null) {
            $this->fail('Next called when it should not be');
        };
    }

    public function nextShouldExpectAndReturn($return, $request, $response, $error = null)
    {
        return function ($req, $res, $err = null) use ($request, $response, $error, $return) {
            $this->assertSame($request, $req);
            $this->assertSame($response, $res);
            $this->assertSame($error, $err);

            return $return;
        };
    }

    public function createUriMock()
    {
        return $this->prophesize(Uri::class);
    }
}
