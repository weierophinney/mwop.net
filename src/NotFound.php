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
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Template\TemplateRendererInterface;

class NotFound implements MiddlewareInterface
{
    const TEMPLATE_NOTFOUND = 'error::404';
    const TEMPLATE_ERROR = 'error::500';

    private $debug;

    private $renderer;

    public function __construct(
        bool $debug,
        TemplateRendererInterface $renderer
    ) {
        $this->debug = $debug;
        $this->renderer = $renderer;
    }

    /**
     * @return HtmlResponse
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        if (false !== $request->getAttribute(RouteResult::class, false)) {
            $view = $this->debug ? ['error' => 'An inner middleware did not return a response'] : [];
            return new HtmlResponse(
                $this->renderer->render(self::TEMPLATE_ERROR, $view),
                500
            );
        }

        return new HtmlResponse(
            $this->renderer->render(self::TEMPLATE_NOTFOUND),
            404
        );
    }
}
