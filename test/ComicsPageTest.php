<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Mwop\ComicsPage;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class ComicsPageTest extends TestCase
{
    use HttpMessagesTrait;

    public function setUp()
    {
        $this->renderer = $this->prophesize(TemplateRendererInterface::class);
        $this->middleware = new ComicsPage('comics', $this->renderer->reveal());
    }

    public function testMiddlewareInvokesNextErrorMiddlewareWith401StatusIfUserAttributeIsMissing()
    {
        $middleware = $this->middleware;
        $request = $this->createRequestMock();
        $response = $this->createResponseMock();
        $finalResponse = $this->createResponseMock()->reveal();

        $request->getAttribute('user', false)->willReturn(false);
        $response->withStatus(401)->willReturn($finalResponse);
        $next = $this->nextShouldExpectAndReturn(
            $finalResponse,
            $request->reveal(),
            $finalResponse,
            401
        );
        $this->renderer->render(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->assertSame(
            $finalResponse,
            $middleware($request->reveal(), $response->reveal(), $next)
        );
    }

    public function testMiddlewareReturnsHtmlResponseInjectedWithResultsOfRendereringPage()
    {
        $middleware = $this->middleware;
        $request = $this->createRequestMock();

        $request->getAttribute('user', false)->willReturn('mwop');
        $this->renderer->render('comics', [])->willReturn('content')->shouldBeCalled();
        $response = $middleware(
            $request->reveal(),
            $this->createResponseMock()->reveal(),
            $this->nextShouldNotBeCalled()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
