<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Contact\Handler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Template\TemplateRendererInterface;

class DisplayContactFormHandler implements RequestHandlerInterface
{
    /** @var array<string, mixed> */
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

    public function handle(Request $request) : Response
    {
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);

        $view = array_merge($this->config, [
            'csrf'   => $guard->generateToken(),
        ]);

        return new HtmlResponse(
            $this->template->render('contact::landing', $view)
        );
    }
}
