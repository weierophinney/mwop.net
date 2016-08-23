<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Mwop\Unauthorized;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class UnauthorizedTest extends TestCase
{
    use HttpMessagesTrait;

    public function setUp()
    {
        $this->renderer = $this->prophesize(TemplateRendererInterface::class);
        $this->middleware = new Unauthorized($this->renderer->reveal());
    }

    public function testReturnsNextMiddlewareWhenStatusCodeIsNot401()
    {
        $middleware = $this->middleware;
        $request = $this->createRequestMock();
        $response = $this->createResponseMock();
        $error = 'error';
        $expected = $this->createResponseMock()->reveal();

        $response->getStatusCode()->willReturn(200);
        $request->getUri()->shouldNotBeCalled();
        $this->renderer->render(Argument::any(), Argument::any())->shouldNotBeCalled();

        $next = $this->nextShouldExpectAndReturn($expected, $request->reveal(), $response->reveal(), $error);

        $result = $middleware($error, $request->reveal(), $response->reveal(), $next);
        $this->assertSame($expected, $result);
    }

    public function testMiddlewareReturnsHtmlResponseInjectedWithResultsOfRendereringTemplat()
    {
        $middleware = $this->middleware;
        $request = $this->createRequestMock();
        $originalRequest = $this->createRequestMock();
        $uri = $this->createUriMock();
        $response = $this->createResponseMock();
        $next = $this->nextShouldNotBeCalled();
        $error = 'error';
        $view = [
            'auth_path' => '/auth',
            'redirect' => '/foo',
        ];

        $response->getStatusCode()->willReturn(401);
        $request->getUri()->will([$uri, 'reveal']);
        $request->getOriginalRequest()->will([$originalRequest, 'reveal']);
        $uri->withPath('/auth')->will([$uri, 'reveal']);
        $uri->__toString()->willReturn('/auth');
        $originalRequest->getUri()->willReturn('/foo');
        $this->renderer->render('error::401', $view)->willReturn('unauthorized');

        $result = $middleware($error, $request->reveal(), $response->reveal(), $next);

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertEquals('unauthorized', (string) $result->getBody());
        $this->assertEquals(401, $result->getStatusCode());
    }
}
