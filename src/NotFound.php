<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Template\TemplateRendererInterface;

class NotFound
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

    public function __invoke(Request $request, Response $response, callable $next) : Response
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
