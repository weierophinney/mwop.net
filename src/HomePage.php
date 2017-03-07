<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePage implements MiddlewareInterface
{
    const TEMPLATE = 'mwop::home.page';

    private $posts;
    private $renderer;

    public function __construct(
        array $posts,
        TemplateRendererInterface $renderer
    ) {
        $this->posts    = $posts ;
        $this->renderer = $renderer;
    }

    /**
     * @return HtmlResponse
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        return new HtmlResponse(
            $this->renderer->render(self::TEMPLATE, [
                'posts' => $this->posts,
            ])
        );
    }
}
