<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Contact\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

use function array_merge;

class DisplayContactFormHandler implements RequestHandlerInterface
{
    /** @var array array<string, mixed> */
    private $config;

    /** @var TemplateRendererInterface */
    private $template;

    public function __construct(
        TemplateRendererInterface $template,
        array $config
    ) {
        $this->template = $template;
        $this->config   = $config;
    }

    public function handle(Request $request): Response
    {
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);

        $view = array_merge($this->config, [
            'csrf' => $guard->generateToken(),
        ]);

        return new HtmlResponse(
            $this->template->render('contact::landing', $view)
        );
    }
}
