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
        $request    = $this->createRequestMock();
        $response   = $this->createResponseMock();
        $response
            ->expects($this->atLeastOnce())
            ->method('withHeader')
            ->with('X-Clacks-Overhead', 'GNU Terry Pratchett')
            ->willReturnSelf();
        $handler = $this->handlerShouldExpectAndReturn(
            $response,
            $request
        );

        $this->assertSame(
            $response,
            $middleware->process($request, $handler)
        );
    }
}
