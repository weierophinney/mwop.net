<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class Page implements RequestHandlerInterface
{
    private $page;
    private $template;

    public function __construct(string $page, TemplateRendererInterface $template)
    {
        $this->page      = $page;
        $this->template  = $template;
    }

    public function handle(Request $request) : Response
    {
        return new HtmlResponse(
            $this->template->render($this->page, [])
        );
    }
}
