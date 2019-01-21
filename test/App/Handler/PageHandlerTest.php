<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest\App\Handler;

use Mwop\App\Handler\PageHandler;
use MwopTest\HttpMessagesTrait;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class PageHandlerTest extends TestCase
{
    use HttpMessagesTrait;

    public function testMiddlewareReturnsHtmlResponseInjectedWithResultsOfRendereringPage()
    {
        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $page = new PageHandler('foo', $renderer->reveal());

        $renderer->render('foo', [])->willReturn('content')->shouldBeCalled();
        $response = $page->handle(
            $this->createRequestMock()->reveal()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
