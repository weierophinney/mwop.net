<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Mwop\Page;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class PageTest extends TestCase
{
    use HttpMessagesTrait;

    public function testMiddlewareReturnsHtmlResponseInjectedWithResultsOfRendereringPage()
    {
        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $middleware = new Page('foo', $renderer->reveal());

        $renderer->render('foo', [])->willReturn('content')->shouldBeCalled();
        $response = $middleware(
            $this->createRequestMock()->reveal(),
            $this->createResponseMock()->reveal(),
            $this->nextShouldNotBeCalled()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
