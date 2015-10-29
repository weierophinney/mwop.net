<?php
namespace Mwop\Contact;

use Aura\Session\Session;
use Mwop\PageView;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LandingPage
{
    private $config;
    private $router;
    private $session;
    private $template;

    public function __construct(
        TemplateRendererInterface $template,
        RouterInterface $router,
        Session $session,
        array $config
    ) {
        $this->template = $template;
        $this->router   = $router;
        $this->session  = $session;
        $this->config   = $config;
    }

    public function __invoke($request, $response, $next)
    {
        $basePath = $request->getOriginalRequest()->getUri()->getPath();
        $view = new PageView(array_merge($this->config, [
            'action' => rtrim($basePath, '/') . '/process',
            'csrf'   => $this->session->getCsrfToken()->getValue(),
        ]));
        $view->setRouter($this->router);

        return new HtmlResponse(
            $this->template->render('contact::landing', $view)
        );
    }
}
