<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Mwop\Page;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class PageTest extends TestCase
{
    use HttpMessagesTrait;

    public function testMiddlewareReturnsHtmlResponseInjectedWithResultsOfRendereringPage()
    {
        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $page = new Page('foo', $renderer->reveal());

        $renderer->render('foo', [])->willReturn('content')->shouldBeCalled();
        $response = $page->handle(
            $this->createRequestMock()->reveal()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
