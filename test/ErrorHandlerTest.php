<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Exception;
use Mwop\ErrorHandler;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;
use TypeError;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Template\TemplateRendererInterface;

class ErrorHandlerTest extends TestCase
{
    use HttpMessagesTrait;

    public function setUp()
    {
        $this->renderer = $this->prophesize(TemplateRendererInterface::class);
    }

    public function debugValues()
    {
        return [
            'true' => [true],
            'false' => [false],
        ];
    }

    public function testGetErrorFormatterReturnsCallable()
    {
        $handler = new ErrorHandler($this->renderer->reveal(), true);
        $formatter = $handler->getErrorFormatter();
        $this->assertInternalType('callable', $formatter);
        return [
            'handler' => $handler,
            'formatter' => $formatter,
        ];
    }

    /**
     * @depends testGetErrorFormatterReturnsCallable
     */
    public function testErrorFormatterIsMutable(array $originals)
    {
        $handler = $originals['handler'];
        $new = clone $originals['formatter'];
        $handler->setErrorFormatter($new);
        $this->assertSame($new, $handler->getErrorFormatter());
    }

    public function testReturnsOriginalResponseIfNextReturnsResponse()
    {
        $request = $this->createRequestMock()->reveal();
        $response = $this->createResponseMock()->reveal();
        $next = $this->nextShouldExpectAndReturn(
            $response,
            $request,
            $response
        );

        $handler = new ErrorHandler($this->renderer->reveal(), true);
        $result = $handler($request, $response, $next);
        $this->assertSame($response, $result);
    }

    public function throwables()
    {
        return [
            'exception' => [new Exception('exception message', 500)],
            'throwable' => [new TypeError('throwable message', 501)],
        ];
    }

    /**
     * @dataProvider throwables
     */
    public function testRendersErrorTemplateWithoutErrorWhenDebugIsDisabledAndThrowableCaught($throwable)
    {
        $next = function () use ($throwable) {
            throw $throwable;
        };

        $this->renderer->render(ErrorHandler::TEMPLATE_ERROR)->willReturn('error content');

        $handler = new ErrorHandler($this->renderer->reveal(), false);
        $result = $handler(
            $this->createRequestMock()->reveal(),
            $this->createResponseMock()->reveal(),
            $next
        );

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertEquals(500, $result->getStatusCode());
        $this->assertEquals('error content', (string) $result->getBody());
    }

    /**
     * @dataProvider throwables
     */
    public function testInvokesDefaultErrorFormatterWhenDebugIsEnabledAndThrowableCaught($throwable)
    {
        $next = function () use ($throwable) {
            throw $throwable;
        };

        $this->renderer->render(ErrorHandler::TEMPLATE_ERROR, Argument::that(function ($argument) use ($throwable) {
            TestCase::assertInternalType('array', $argument);
            TestCase::assertArrayHasKey('error', $argument);
            $error = $argument['error'];
            TestCase::assertContains($throwable->getMessage(), $error);
            TestCase::assertContains((string) $throwable->getCode(), $error);
            TestCase::assertContains($throwable->getTraceAsString(), $error);
            return true;
        }))->willReturn('error content');

        $handler = new ErrorHandler($this->renderer->reveal(), true);
        $result = $handler(
            $this->createRequestMock()->reveal(),
            $this->createResponseMock()->reveal(),
            $next
        );

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertEquals(500, $result->getStatusCode());
        $this->assertEquals('error content', (string) $result->getBody());
    }

    /**
     * @dataProvider throwables
     */
    public function testInvokesInjectedErrorFormatterWhenDebugIsEnabledAndThrowableCaught($throwable)
    {
        $next = function () use ($throwable) {
            throw $throwable;
        };
        $request = $this->createRequestMock()->reveal();

        $formatter = function ($error, $req) use ($throwable, $request) {
            $this->assertSame($throwable, $error);
            $this->assertSame($request, $req);
            return 'error content';
        };

        $this->renderer->render(Argument::any(), Argument::any())->shouldNotBeCalled();

        $handler = new ErrorHandler($this->renderer->reveal(), true);
        $handler->setErrorFormatter($formatter);
        $result = $handler(
            $request,
            $this->createResponseMock()->reveal(),
            $next
        );

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertEquals(500, $result->getStatusCode());
        $this->assertEquals('error content', (string) $result->getBody());
    }
}
