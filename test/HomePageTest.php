<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Mwop\HomePage;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageTest extends TestCase
{
    use HttpMessagesTrait;

    public function testMiddlewareReturnsHtmlResponseInjectedWithResultsOfRendereringPosts()
    {
        $posts = ['foo', 'bar'];
        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $middleware = new HomePage($posts, $renderer->reveal());

        $renderer->render(HomePage::TEMPLATE, [
            'posts' => $posts,
        ])->willReturn('content')->shouldBeCalled();
        $response = $middleware(
            $this->createRequestMock()->reveal(),
            $this->createResponseMock()->reveal(),
            $this->nextShouldNotBeCalled()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
