<?php

declare(strict_types=1);

namespace MwopTest\App\Middleware;

use Mwop\App\Middleware\RedirectsMiddleware;
use MwopTest\HttpMessagesTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class RedirectsMiddlewareTest extends TestCase
{
    use HttpMessagesTrait;

    public function testMiddlewarePassesPhpNetUrlToDelegate()
    {
        $middleware = new RedirectsMiddleware();
        $uri        = $this->createUriMock();
        $request    = $this->createRequestMock();
        $response   = $this->createResponseMock();

        $request->method('getUri')->willReturn($uri);
        $uri->method('getPath')->willReturn('/blog/tag/php.xml');

        $handler = $this->handlerShouldExpectAndReturn($response, $request);

        $this->assertSame($response, $middleware->process($request, $handler));
    }

    /**
     * @psalm-return array<string, array{
     *     0: string,
     *     1: string,
     *     2: string|null,
     *     3: string
     * }>
     */
    public function expectedRedirects(): array
    {
        // @codingStandardsIgnoreStart
        return [
            // name               => [incoming URL,                        path,                        query   , redirect      ]
            'blog-paginated-urls' => ['/blog-p3.html',                     '/blog',                     'page=3', '/blog?page=3'],
            'blog-paginated-tags' => ['/blog/tag/foo-p3.html',             '/blog/tag/foo',             'page=3', '/blog/tag/foo?page=3'],
            'blog-subpath'        => ['/blog.html/foo',                    '/blog/foo',                 null,     '/blog/foo'],
            'blog-phlyblog-atom'  => ['/blog/tag/foo-atom.xml',            '/blog/tag/foo/atom.xml',    null,     '/blog/tag/foo/atom.xml'],
            'blog-phlyblog-rss'   => ['/blog/tag/foo-rss.xml',             '/blog/tag/foo/rss.xml',     null,     '/blog/tag/foo/rss.xml'],
            'blog-s9y'            => ['/blog/tag/foo.xml',                 '/blog/tag/foo/rss.xml',     null,     '/blog/tag/foo/rss.xml'],
            'blog-post-comma'     => ['/blog/post,-comma.html',            '/blog/post-comma.html',     null,     '/blog/post-comma.html'],
            'blog-post-semicolon' => ['/blog/post;-semi.html',             '/blog/post-semi.html',      null,     '/blog/post-semi.html'],
            'blog-post-exclaim'   => ['/blog/post!-excl.html',             '/blog/post-excl.html',      null,     '/blog/post-excl.html'],
            'blog-post-nosuffix'  => ['/blog/post-wo-suffix',              '/blog/post-wo-suffix.html', null,     '/blog/post-wo-suffix.html'],
            'blog-s9y-rss2'       => ['/matthew/feeds/index.rss2',         '/blog/rss.xml',             null,     '/blog/rss.xml'],
            'blog-s9y-atom'       => ['/matthew/feeds/atom.xml',           '/blog/atom.xml',            null,     '/blog/atom.xml'],
            'blog-s9y-year'       => ['/matthew/archives/2004.html',       '/blog',                     null,     '/blog'],
            'blog-s9y-month'      => ['/matthew/archives/2004/01.html',    '/blog',                     null,     '/blog'],
            'blog-s9y-day'        => ['/matthew/archives/2004/01/01.html', '/blog',                     null,     '/blog'],
            'blog-s9y-post'       => ['/matthew/archives/foo.html',        '/blog/foo.html',            null,     '/blog/foo.html'],
            'blog-s9y-tag'        => ['/matthew/plugin/tag/foo',           '/blog/tag/foo',             null,     '/blog/tag/foo'],
            'blog-s9y-cat-feed'   => ['/matthew/categories/01-foo.rss',    '/blog/tag/foo/rss.xml',     null,     '/blog/tag/foo/rss.xml'],
            'blog-s9y-cat'        => ['/matthew/categories/01-foo',        '/blog/tag/foo',             null,     '/blog/tag/foo'],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @dataProvider expectedRedirects
     */
    public function testMiddlewareRedirectsAsExpected(
        string $incomingUri,
        string $path,
        ?string $query,
        string $redirect
    ) {
        $middleware = new RedirectsMiddleware();
        $uri        = $this->createUriMock();
        $request    = $this->createRequestMock();

        $request->method('getUri')->willReturn($uri);

        $uri->method('getPath')->willReturn($incomingUri);
        $uri->method('withPath')->with($path)->willReturn($uri);

        if ($query) {
            $uri->method('withQuery')->with($query)->willReturn($uri);
        } else {
            $uri->expects($this->never())->method('withQuery');
        }
        $uri->method('__toString')->willReturn($redirect);

        $response = $middleware->process(
            $request,
            $this->handlerShouldNotBeCalled()
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals($redirect, $response->getHeaderLine('Location'));
    }

    /**
     * @psalm-return array<string, array{
     *     0: string,
     *     1: string,
     *     2: string,
     *     3: string,
     *     4: string
     * }>
     */
    public function expectedAlternateHostRedirects(): array
    {
        // @codingStandardsIgnoreStart
        return [
            // case   =>     [original path,          host,                   scheme,  final path, location],
            'uploads' =>     ['/uploads/foo.doc',     'uploads.mwop.net',     'https', '/foo.doc', 'https://uploads.mwop.net/foo.doc'],
            'screencasts' => ['/screencasts/foo.mp4', 'screencasts.mwop.net', 'https', '/foo.mp4', 'https://screencasts.mwop.net/foo.mp4'],
            'slides' =>      ['/slides/foo.pdf',      'slides.mwop.net',      'https', '/foo.pdf', 'https://slides.mwop.net/foo.pdf'],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @dataProvider expectedAlternateHostRedirects
     */
    public function testMiddlewareRedirectsToAlternateHostsWhenExpected(
        string $incomingUri,
        string $hostTo,
        string $schemeTo,
        string $pathTo,
        string $location
    ) {
        $middleware = new RedirectsMiddleware();
        $uri        = $this->createUriMock();
        $request    = $this->createRequestMock();

        $request->method('getUri')->willReturn($uri);

        $uri->method('getPath')->willReturn($incomingUri);
        $uri->method('withHost')->with($hostTo)->willReturn($uri);
        $uri->method('withScheme')->with($schemeTo)->willReturn($uri);
        $uri->method('withPath')->with($pathTo)->willReturn($uri);
        $uri->expects($this->never())->method('withQuery');
        $uri->method('__toString')->willReturn($location);

        $response = $middleware->process(
            $request,
            $this->handlerShouldNotBeCalled()
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals($location, $response->getHeaderLine('Location'));
    }

    public function testS9yTagFeedsRedirectToBlogTagFeeds()
    {
        $middleware = new RedirectsMiddleware();
        $uri        = $this->createUriMock();
        $request    = $this->createRequestMock();

        $request->method('getUri')->willReturn($uri);
        $uri->method('getPath')->willReturn('/matthew/rss.php');
        $request->method('getQueryParams')->willReturn(['serendipity' => ['tag' => 'foo']]);
        $uri->method('withPath')->with('/blog/tag/foo/rss.xml')->willReturn($uri);
        $uri->expects($this->never())->method('withQuery');
        $uri->method('__toString')->willReturn('/blog/tag/foo/rss.xml');

        $response = $middleware->process(
            $request,
            $this->handlerShouldNotBeCalled()
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals('/blog/tag/foo/rss.xml', $response->getHeaderLine('Location'));
    }

    public function testS9yFeedRedirectsToBlogFeed()
    {
        $middleware = new RedirectsMiddleware();
        $uri        = $this->createUriMock();
        $request    = $this->createRequestMock();

        $request->method('getUri')->willReturn($uri);

        $uri->method('getPath')->willReturn('/matthew/rss.php');
        $request->method('getQueryParams')->willReturn([]);
        $uri->method('withPath')->with('/blog/rss.xml')->willReturn($uri);
        $uri->expects($this->never())->method('withQuery');
        $uri->method('__toString')->willReturn('/blog/rss.xml');

        $response = $middleware->process(
            $request,
            $this->handlerShouldNotBeCalled()
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals('/blog/rss.xml', $response->getHeaderLine('Location'));
    }

    public function testS9yBaseRedirectsToBlog()
    {
        $middleware = new RedirectsMiddleware();
        $uri        = $this->createUriMock();
        $request    = $this->createRequestMock();

        $request->method('getUri')->willReturn($uri);

        $uri->method('getPath')->willReturn('/matthew');
        $request->method('getQueryParams')->willReturn([]);
        $uri->method('withPath')->with('/blog')->willReturn($uri);
        $uri->expects($this->never())->method('withQuery');
        $uri->method('__toString')->willReturn('/blog');

        $response = $middleware->process(
            $request,
            $this->handlerShouldNotBeCalled()
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals('/blog', $response->getHeaderLine('Location'));
    }

    public function testInvokesNextIfPathDoesNotMatchARedirect()
    {
        $middleware = new RedirectsMiddleware();
        $uri        = $this->createUriMock();
        $request    = $this->createRequestMock();
        $response   = $this->createResponseMock();

        $request->method('getUri')->willReturn($uri);

        $uri->method('getPath')->willReturn('/comics');

        $handler = $this->handlerShouldExpectAndReturn(
            $response,
            $request
        );

        $this->assertSame($response, $middleware->process($request, $handler));
    }
}
