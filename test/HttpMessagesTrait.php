<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace MwopTest;

use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface as Uri;
use Psr\Http\Server\RequestHandlerInterface;

trait HttpMessagesTrait
{
    /** @return Request|ObjectProphecy */
    public function createRequestMock(): ObjectProphecy
    {
        return $this->prophesize(Request::class);
    }

    /** @return Response|ObjectProphecy */
    public function createResponseMock(): ObjectProphecy
    {
        return $this->prophesize(Response::class);
    }

    public function handlerShouldNotBeCalled(): RequestHandlerInterface
    {
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle(Argument::any())->shouldNotBeCalled();
        return $handler->reveal();
    }

    /**
     * @param mixed $return
     */
    public function handlerShouldExpectAndReturn($return, ServerRequestInterface $request): RequestHandlerInterface
    {
        $requestExpectation = Argument::that(function ($argument) use ($request) {
            Assert::assertSame($request, $argument, 'Request passed to handler does not match expectation');
            return true;
        });
        $handler            = $this->prophesize(RequestHandlerInterface::class);
        $handler
            ->handle($requestExpectation)
            ->willReturn($return);

        return $handler->reveal();
    }

    /** @return UriInterface|ObjectProphecy */
    public function createUriMock(): ObjectProphecy
    {
        return $this->prophesize(Uri::class);
    }
}
