<?php

declare(strict_types=1);

namespace MwopTest\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\App\Handler\HomePageHandler;
use MwopTest\HttpMessagesTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HomePageHandlerTest extends TestCase
{
    use HttpMessagesTrait;

    public function testMiddlewareReturnsHtmlResponseInjectedWithResultsOfRendereringPosts()
    {
        /** @var TemplateRendererInterface|MockObject $renderer */
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $handler  = new HomePageHandler($renderer);

        $renderer
            ->expects($this->atLeastOnce())
            ->method('render')
            ->with(HomePageHandler::TEMPLATE, [])
            ->willReturn('content');

        $response = $handler->handle(
            $this->createRequestMock()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
