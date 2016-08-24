<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Mwop\NotFound;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Template\TemplateRendererInterface;

class NotFoundTest extends TestCase
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

    /**
     * @dataProvider debugValues
     */
    public function testReturnsHtmlResponseWithRenderedNotFoundTemplateWhenRouteResultNotPresent($debug)
    {
        $this->renderer->render(NotFound::TEMPLATE_NOTFOUND)->willReturn('404 content');
        $middleware = new NotFound($debug, $this->renderer->reveal());

        $request = $this->createRequestMock();
        $request->getAttribute(RouteResult::class, false)->willReturn(false);
        $response = $this->createResponseMock()->reveal();
        $next = $this->nextShouldNotBeCalled();

        $result = $middleware($request->reveal(), $response, $next);

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertEquals(404, $result->getStatusCode());
        $this->assertEquals('404 content', (string) $result->getBody());
    }

    public function debugErrorValues()
    {
        return [
            'true' => [true, ['error' => 'An inner middleware did not return a response']],
            'false' => [false, []],
        ];
    }

    /**
     * @dataProvider debugErrorValues
     */
    public function testReturnsHtmlResponseWithRenderedErrorTemplateWhenRouteResultPresent($debug, $expectedView)
    {
        $this->renderer->render(NotFound::TEMPLATE_ERROR, $expectedView)->willReturn('500 content');
        $middleware = new NotFound($debug, $this->renderer->reveal());

        $request = $this->createRequestMock();
        $request->getAttribute(RouteResult::class, false)->willReturn(true);
        $response = $this->createResponseMock()->reveal();
        $next = $this->nextShouldNotBeCalled();

        $result = $middleware($request->reveal(), $response, $next);

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertEquals(500, $result->getStatusCode());
        $this->assertEquals('500 content', (string) $result->getBody());
    }
}
