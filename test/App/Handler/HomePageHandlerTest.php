<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

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
        $posts    = ['foo', 'bar'];
        /** @var TemplateRendererInterface|MockObject $renderer */
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $handler  = new HomePageHandler($posts, '', $renderer);

        $renderer
            ->expects($this->atLeastOnce())
            ->method('render')
            ->with(HomePageHandler::TEMPLATE, [
                'posts'     => $posts,
                'instagram' => [],
            ])
            ->willReturn('content');

        $response = $handler->handle(
            $this->createRequestMock()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
