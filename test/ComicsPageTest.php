<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Mwop\ComicsPage;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class ComicsPageTest extends TestCase
{
    use HttpMessagesTrait;

    public function setUp()
    {
        $this->renderer = $this->prophesize(TemplateRendererInterface::class);
    }

    public function testMiddlewareReturnsInvokes401HandlerIfUserAttributeIsMissing()
    {
        $expectedResponse = $this->createResponseMock()->reveal();
        $unauthFactory = function (Request $request) use ($expectedResponse) {
            return $expectedResponse;
        };

        $middleware = new ComicsPage($this->renderer->reveal(), $unauthFactory);

        $request = $this->createRequestMock();

        $request->getAttribute('user', false)->willReturn(false);
        $delegate = $this->delegateShouldExpectAndReturn(
            $expectedResponse,
            $request->reveal()
        );
        $this->renderer->render(Argument::any())->shouldNotBeCalled();

        $this->assertSame(
            $expectedResponse,
            $middleware->process($request->reveal(), $delegate)
        );
    }

    public function testMiddlewareReturnsHtmlResponseInjectedWithResultsOfRendereringPage()
    {
        $unauthFactory = function (Request $request) {
            $this->fail('Factory for generating unauthorized response was invoked when it should not be');
        };

        $middleware = new ComicsPage($this->renderer->reveal(), $unauthFactory);
        $request = $this->createRequestMock();

        $request->getAttribute('user', false)->willReturn('mwop');
        $this->renderer->render('mwop::comics.page')->willReturn('content')->shouldBeCalled();
        $response = $middleware->process(
            $request->reveal(),
            $this->delegateShouldNotBeCalled()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
