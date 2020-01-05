<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest\App\Handler;

use Mwop\App\Handler\HomePageHandler;
use MwopTest\HttpMessagesTrait;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class HomePageHandlerTest extends TestCase
{
    use HttpMessagesTrait;

    public function testMiddlewareReturnsHtmlResponseInjectedWithResultsOfRendereringPosts()
    {
        $posts = ['foo', 'bar'];
        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $handler = new HomePageHandler($posts, $renderer->reveal());

        $renderer->render(HomePageHandler::TEMPLATE, [
            'posts' => $posts,
        ])->willReturn('content')->shouldBeCalled();

        $response = $handler->handle(
            $this->createRequestMock()->reveal()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
