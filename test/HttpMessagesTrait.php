<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace MwopTest;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface as Uri;
use Psr\Http\Server\RequestHandlerInterface;

trait HttpMessagesTrait
{
    /** @return Request|MockObject */
    public function createRequestMock()
    {
        return $this->createMock(Request::class);
    }

    /** @return Response|MockObject */
    public function createResponseMock()
    {
        return $this->createMock(Response::class);
    }

    /** @return RequestHandlerInterface|MockObject */
    public function handlerShouldNotBeCalled()
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->createMock(RequestHandlerInterface::class);
        /** @var TestCase $this */
        $handler->expects($this->never())->method('handle');
        return $handler;
    }

    /**
     * @param RequestHandlerInterface|MockObject $return
     */
    public function handlerShouldExpectAndReturn($return, ServerRequestInterface $request)
    {
        /** @var TestCase $this */
        $handler = $this->createMock(RequestHandlerInterface::class);

        /** @var RequestHandlerInterface|MockObject */
        $handler
            ->method('handle')
            ->with($this->callback(function (ServerRequestInterface $actualRequest) use ($request): bool {
                return $request === $actualRequest;
            }))
            ->willReturn($return);


        return $handler;
    }

    /** @return Uri|MockObject */
    public function createUriMock()
    {
        return $this->createMock(Uri::class);
    }
}
