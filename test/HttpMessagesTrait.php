<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface as Uri;
use Zend\Stratigility\Http\Request as StratigilityRequest;

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

    public function delegateShouldNotBeCalled()
    {
        $delegate = $this->prophesize(DelegateInterface::class);
        $delegate->process(Argument::any())->shouldNotBeCalled();
        return $delegate->reveal();
    }

    public function delegateShouldExpectAndReturn($return, $request)
    {
        $requestExpectation = Argument::that(function ($argument) use ($request) {
            Assert::assertSame($request, $argument, 'Request passed to delegate does not match expectation');
            return true;
        });
        $delegate = $this->prophesize(DelegateInterface::class);
        $delegate->process($requestExpectation)
            ->willReturn($return);

        return $delegate->reveal();
    }

    public function createUriMock()
    {
        return $this->prophesize(Uri::class);
    }
}
