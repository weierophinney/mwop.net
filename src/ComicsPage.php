<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class ComicsPage implements MiddlewareInterface
{
    const TEMPLATE = 'mwop::comics.page';

    private $renderer;

    private $unauthorizedResponseFactory;

    public function __construct(TemplateRendererInterface $renderer, callable $unauthorizedResponseFactory)
    {
        $this->renderer = $renderer;
        $this->unauthorizedResponseFactory = $unauthorizedResponseFactory;
    }

    /**
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        if (! $request->getAttribute('user', false)) {
            $factory = $this->unauthorizedResponseFactory;
            return $factory($request);
        }

        return new HtmlResponse(
            $this->renderer->render(self::TEMPLATE)
        );
    }
}
