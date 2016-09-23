<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePage
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

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        return new HtmlResponse(
            $this->renderer->render(self::TEMPLATE, [
                'posts' => $this->posts,
            ])
        );
    }
}
