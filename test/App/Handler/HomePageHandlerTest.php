<?php

declare(strict_types=1);

namespace MwopTest\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Paginator\Paginator;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\App\Handler\HomePageHandler;
use Mwop\App\MastodonFeed;
use Mwop\Art\PhotoMapper;
use Mwop\Mastodon\Collection;
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
        /** @var PhotoMapper|MockObject $photos */
        $photos = $this->createMock(PhotoMapper::class);
        /** @var Paginator|MockObject $paginator */
        $paginator = $this->createMock(Paginator::class);
        /** @var MastodonFeed|MockObject $mastodon */
        $mastodon   = $this->createMock(MastodonFeed::class);
        $collection = new Collection([]);

        $photos
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn($paginator);

        $paginator
            ->expects($this->once())
            ->method('setItemCountPerPage')
            ->with($this->isType('int'));
        $paginator
            ->expects($this->once())
            ->method('setCurrentPageNumber')
            ->with(1);

        $mastodon
            ->expects($this->once())
            ->method('read')
            ->willReturn($collection);

        $renderer
            ->expects($this->atLeastOnce())
            ->method('render')
            ->with(HomePageHandler::TEMPLATE, [
                'photos' => $paginator,
                'mastodon' => $collection,
            ])
            ->willReturn('content');

        $handler  = new HomePageHandler($photos, $mastodon, $renderer);
        $response = $handler->handle(
            $this->createRequestMock()
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('content', (string) $response->getBody());
    }
}
