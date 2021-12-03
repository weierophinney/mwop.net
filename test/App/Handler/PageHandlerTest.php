<?php

declare(strict_types=1);

namespace MwopTest\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\App\Handler\PageHandler;
use MwopTest\HttpMessagesTrait;
use PHPUnit\Framework\TestCase;

class PageHandlerTest extends TestCase
{
    use HttpMessagesTrait;

    public function testMiddlewareReturnsHtmlResponseInjectedWithResultsOfRendereringPage()
    {
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $page     = new PageHandler('foo', $renderer);

        $renderer
            ->expects($this->atLeastOnce())
            ->method('render')
            ->with('foo', [])
            ->willReturn('content');

        $response = $page->handle(
            $this->createRequestMock()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
