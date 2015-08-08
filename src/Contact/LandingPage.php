<?php
namespace Mwop\Contact;

use Aura\Session\Session;
use Mwop\Template\TemplateInterface;
use Zend\Diactoros\Response\HtmlResponse;

class LandingPage
{
    private $config;
    private $session;
    private $template;

    public function __construct(TemplateInterface $template, Session $session, array $config)
    {
        $this->template = $template;
        $this->session  = $session;
        $this->config   = $config;
    }

    public function __invoke($request, $response, $next)
    {
        $basePath = $request->getOriginalRequest()->getUri()->getPath();
        $view = array_merge($this->config, [
            'action' => rtrim($basePath, '/') . '/process',
            'csrf'   => $this->session->getCsrfToken()->getValue(),
        ]);

        return new HtmlResponse(
            $this->template->render('contact.landing', $view)
        );
    }
}
