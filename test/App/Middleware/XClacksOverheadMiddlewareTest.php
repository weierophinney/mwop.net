<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace MwopTest\App\Middleware;

use Mwop\App\Middleware\XClacksOverheadMiddleware;
use MwopTest\HttpMessagesTrait;
use PHPUnit\Framework\TestCase;

class XClacksOverheadMiddlewareTest extends TestCase
{
    use HttpMessagesTrait;

    public function testMiddlewareInjectsResponseReturnedByNextWithXClacksOverheadHeader()
    {
        $middleware = new XClacksOverheadMiddleware();
        $request    = $this->createRequestMock()->reveal();
        $response   = $this->createResponseMock();
        $response
            ->withHeader('X-Clacks-Overhead', 'GNU Terry Pratchett')
            ->will([$response, 'reveal'])
            ->shouldBeCalled();
        $handler = $this->handlerShouldExpectAndReturn(
            $response->reveal(),
            $request
        );

        $this->assertSame(
            $response->reveal(),
            $middleware->process($request, $handler)
        );
    }
}
