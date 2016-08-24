<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Mwop\Redirects;
use Prophecy\Argument;
use PHPUnit_Framework_TestCase as TestCase;

class RedirectsTest extends TestCase
{
    use HttpMessagesTrait;

    public function testMiddlewarePassesPhpNetUrlToNext()
    {
        $middleware = new Redirects();
        $uri = $this->createUriMock();
        $request = $this->createRequestMock();
        $request->getUri()->will([$uri, 'reveal']);
        $response = $this->createResponseMock()->reveal();

        $uri->getPath()->willReturn('/blog/tag/php.xml');

        $next = $this->nextShouldExpectAndReturn($response, $request->reveal(), $response);

        $this->assertSame($response, $middleware($request->reveal(), $response, $next));
    }

    public function expectedRedirects()
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
    public function testMiddlewareRedirectsAsExpected($incomingUri, $path, $query, $redirect)
    {
        $middleware = new Redirects();
        $uri = $this->createUriMock();
        $request = $this->createRequestMock();
        $request->getUri()->will([$uri, 'reveal']);
        $response = $this->createResponseMock();

        $uri->getPath()->willReturn($incomingUri);
        $uri->withPath($path)->will([$uri, 'reveal']);
        if ($query) {
            $uri->withQuery($query)->will([$uri, 'reveal']);
        } else {
            $uri->withQuery(Argument::any())->shouldNotBeCalled();
        }
        $uri->__toString()->willReturn($redirect);

        $response->withStatus(301)->will([$response, 'reveal']);
        $response->withHeader('Location', $redirect)->will([$response, 'reveal']);

        $next = $this->nextShouldNotBeCalled();

        $this->assertSame($response->reveal(), $middleware($request->reveal(), $response->reveal(), $next));
    }

    public function expectedAlternateHostRedirects()
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
        $incomingUri,
        $hostTo,
        $schemeTo,
        $pathTo,
        $location
    ) {
        $middleware = new Redirects();
        $uri = $this->createUriMock();
        $request = $this->createRequestMock();
        $request->getUri()->will([$uri, 'reveal']);
        $response = $this->createResponseMock();

        $uri->getPath()->willReturn($incomingUri);
        $uri->withHost($hostTo)->will([$uri, 'reveal']);
        $uri->withScheme($schemeTo)->will([$uri, 'reveal']);
        $uri->withPath($pathTo)->will([$uri, 'reveal']);
        $uri->withQuery(Argument::any())->shouldNotBeCalled();
        $uri->__toString()->willReturn($location);

        $response->withStatus(301)->will([$response, 'reveal']);
        $response->withHeader('Location', $location)->will([$response, 'reveal']);

        $next = $this->nextShouldNotBeCalled();

        $this->assertSame($response->reveal(), $middleware($request->reveal(), $response->reveal(), $next));
    }

    public function testS9yTagFeedsRedirectToBlogTagFeeds()
    {
        $middleware = new Redirects();
        $uri = $this->createUriMock();
        $request = $this->createRequestMock();
        $request->getUri()->will([$uri, 'reveal']);
        $response = $this->createResponseMock();

        $uri->getPath()->willReturn('/matthew/rss.php');
        $request->getQueryParams()->willReturn(['serendipity' => ['tag' => 'foo']]);
        $uri->withPath('/blog/tag/foo/rss.xml')->will([$uri, 'reveal']);
        $uri->withQuery(Argument::any())->shouldNotBeCalled();
        $uri->__toString()->willReturn('/blog/tag/foo/rss.xml');

        $response->withStatus(301)->will([$response, 'reveal']);
        $response->withHeader('Location', '/blog/tag/foo/rss.xml')->will([$response, 'reveal']);

        $next = $this->nextShouldNotBeCalled();

        $this->assertSame($response->reveal(), $middleware($request->reveal(), $response->reveal(), $next));
    }

    public function testS9yFeedRedirectsToBlogFeed()
    {
        $middleware = new Redirects();
        $uri = $this->createUriMock();
        $request = $this->createRequestMock();
        $request->getUri()->will([$uri, 'reveal']);
        $response = $this->createResponseMock();

        $uri->getPath()->willReturn('/matthew/rss.php');
        $request->getQueryParams()->willReturn([]);
        $uri->withPath('/blog/rss.xml')->will([$uri, 'reveal']);
        $uri->withQuery(Argument::any())->shouldNotBeCalled();
        $uri->__toString()->willReturn('/blog/rss.xml');

        $response->withStatus(301)->will([$response, 'reveal']);
        $response->withHeader('Location', '/blog/rss.xml')->will([$response, 'reveal']);

        $next = $this->nextShouldNotBeCalled();

        $this->assertSame($response->reveal(), $middleware($request->reveal(), $response->reveal(), $next));
    }

    public function testS9yBaseRedirectsToBlog()
    {
        $middleware = new Redirects();
        $uri = $this->createUriMock();
        $request = $this->createRequestMock();
        $request->getUri()->will([$uri, 'reveal']);
        $response = $this->createResponseMock();

        $uri->getPath()->willReturn('/matthew');
        $request->getQueryParams()->willReturn([]);
        $uri->withPath('/blog')->will([$uri, 'reveal']);
        $uri->withQuery(Argument::any())->shouldNotBeCalled();
        $uri->__toString()->willReturn('/blog');

        $response->withStatus(301)->will([$response, 'reveal']);
        $response->withHeader('Location', '/blog')->will([$response, 'reveal']);

        $next = $this->nextShouldNotBeCalled();

        $this->assertSame($response->reveal(), $middleware($request->reveal(), $response->reveal(), $next));
    }

    public function testInvokesNextIfPathDoesNotMatchARedirect()
    {
        $middleware = new Redirects();
        $uri = $this->createUriMock();
        $request = $this->createRequestMock();
        $request->getUri()->will([$uri, 'reveal']);
        $response = $this->createResponseMock()->reveal();

        $uri->getPath()->willReturn('/comics');

        $next = $this->nextShouldExpectAndReturn(
            $response,
            $request->reveal(),
            $response
        );

        $this->assertSame($response, $middleware($request->reveal(), $response, $next));
    }
}
