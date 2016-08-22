<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Contact;

use Aura\Session\Session;
use Mwop\PageView;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class LandingPage
{
    private $config;
    private $session;
    private $template;

    public function __construct(
        TemplateRendererInterface $template,
        Session $session,
        array $config
    ) {
        $this->template = $template;
        $this->session  = $session;
        $this->config   = $config;
    }

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $basePath = $request->getOriginalRequest()->getUri()->getPath();
        $view = array_merge($this->config, [
            'action' => rtrim($basePath, '/') . '/process',
            'csrf'   => $this->session->getCsrfToken()->getValue(),
        ]);

        return new HtmlResponse(
            $this->template->render('contact::landing', $view)
        );
    }
}
