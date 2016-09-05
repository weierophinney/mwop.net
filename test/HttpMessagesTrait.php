<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use PHPUnit_Framework_Assert as Assert;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface as Uri;
use Zend\Stratigility\Http\Request as StratigilityRequest;
use Zend\Stratigility\Next;

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
        $next = $this->prophesize(Next::class);
        $next->__invoke(Argument::any(), Argument::any())->shouldNotBeCalled();
        return $next->reveal();
    }

    public function nextShouldExpectAndReturn($return, $request, $response)
    {
        $requestExpectation = Argument::that(function ($argument) use ($request) {
            Assert::assertSame($request, $argument, 'Request passed to next does not match expectation');
            return true;
        });
        $responseExpectation = Argument::that(function ($argument) use ($response) {
            Assert::assertSame($response, $argument, 'Response passed to next does not match expectation');
            return true;
        });
        $next = $this->prophesize(Next::class);
        $next->__invoke($requestExpectation, $responseExpectation)
            ->willReturn($return);

        return $next->reveal();
    }

    public function createUriMock()
    {
        return $this->prophesize(Uri::class);
    }
}
