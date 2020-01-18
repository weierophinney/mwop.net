<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class PageHandler implements RequestHandlerInterface
{
    private $page;
    private $template;

    public function __construct(string $page, TemplateRendererInterface $template)
    {
        $this->page     = $page;
        $this->template = $template;
    }

    public function handle(Request $request): Response
    {
        return new HtmlResponse(
            $this->template->render($this->page, [])
        );
    }
}
