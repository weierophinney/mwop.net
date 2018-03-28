<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface as Uri;
use Psr\Http\Server\RequestHandlerInterface;

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

    public function handlerShouldNotBeCalled()
    {
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle(Argument::any())->shouldNotBeCalled();
        return $handler->reveal();
    }

    public function handlerShouldExpectAndReturn($return, $request)
    {
        $requestExpectation = Argument::that(function ($argument) use ($request) {
            Assert::assertSame($request, $argument, 'Request passed to handler does not match expectation');
            return true;
        });
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler
            ->handle($requestExpectation)
            ->willReturn($return);

        return $handler->reveal();
    }

    public function createUriMock()
    {
        return $this->prophesize(Uri::class);
    }
}
