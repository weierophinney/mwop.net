<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace MwopTest;

use Interop\Container\ContainerInterface;
use Mwop\UnauthorizedResponseFactoryFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class UnauthorizedResponseFactoryTest extends TestCase
{
    use HttpMessagesTrait;

    public function setUp()
    {
        $this->renderer = $this->prophesize(TemplateRendererInterface::class);
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->container->get('config')->willReturn([]);
        $this->container->get(TemplateRendererInterface::class)->will([$this->renderer, 'reveal']);

        $factory = new UnauthorizedResponseFactoryFactory();
        $this->factory = $factory($this->container->reveal());
    }

    public function testReturnsHtmlResponseInjectedWithResultsOfRendereringTemplate()
    {
        $factory = $this->factory;
        $request = $this->createRequestMock();
        $originalRequest = $this->createRequestMock();
        $uri = $this->createUriMock();
        $error = 'error';
        $view = [
            'auth_path' => '/auth',
            'redirect' => '/foo',
            'debug' => false,
        ];

        $request->getUri()->will([$uri, 'reveal']);
        $request
            ->getAttribute('originalRequest', Argument::type(ServerRequestInterface::class))
            ->will([$originalRequest, 'reveal']);
        $uri->withPath('/auth')->will([$uri, 'reveal']);
        $uri->__toString()->willReturn('/auth');
        $originalRequest->getUri()->willReturn('/foo');
        $this->renderer->render('error::401', $view)->willReturn('unauthorized');

        $result = $factory($request->reveal());

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertEquals('unauthorized', (string) $result->getBody());
        $this->assertEquals(401, $result->getStatusCode());
    }
}
