<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Exception;
use Interop\Container\ContainerInterface;
use Mwop\ErrorHandler;
use Mwop\Factory\FinalHandlerFactory;
use PHPUnit_Framework_TestCase as TestCase;
use TypeError;

class FinalHandlerTest extends TestCase
{
    use HttpMessagesTrait;

    public function setUp()
    {
        $this->errorHandler = $this->prophesize(ErrorHandler::class);
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->container->get(ErrorHandler::class)->will([$this->errorHandler, 'reveal']);

        $factory = new FinalHandlerFactory();
        $this->handler = $factory($this->container->reveal());
    }

    public function testReturnsOriginalResponseWhenNoErrorPresent()
    {
        $handler = $this->handler;
        $response = $this->createResponseMock()->reveal();

        $result = $handler($this->createRequestMock()->reveal(), $response);
        $this->assertSame($response, $result);
    }

    public function throwables()
    {
        return [
            'exception' => [new Exception()],
            'throwable' => [new TypeError()],
        ];
    }

    /**
     * @dataProvider throwables
     */
    public function testInvokesErrorHandlerWhenThrowableErrorPresent($throwable)
    {
        $handler = $this->handler;
        $request = $this->createRequestMock()->reveal();
        $response = $this->createResponseMock()->reveal();
        $generatedResponse = $this->createResponseMock()->reveal();
        $this->errorHandler->createErrorResponse($throwable, $request)->willReturn($generatedResponse);

        $result = $handler($request, $response, $throwable);
        $this->assertSame($generatedResponse, $result);
    }
}
