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
use Zend\Expressive\Template\TemplateRendererInterface;

class Page implements MiddlewareInterface
{
    private $page;
    private $template;

    public function __construct(string $page, TemplateRendererInterface $template)
    {
        $this->page      = $page;
        $this->template  = $template;
    }

    /**
     * @return HtmlResponse
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        return new HtmlResponse(
            $this->template->render($this->page, [])
        );
    }
}
