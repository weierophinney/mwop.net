<?php
namespace Mwop\Contact;

use Mwop\PageView;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ThankYouPage
{
    private $router;
    private $template;

    public function __construct(TemplateRendererInterface $template, RouterInterface $router)
    {
        $this->template = $template;
        $this->router   = $router;
    }

    public function __invoke($request, $response, $next)
    {
        $parent    = $request->getOriginalRequest();
        $parentUrl = str_replace('/thank-you', '', (string) $parent->getUri());
        if (! $request->hasHeader('Referer')
            || ! preg_match('#^' . $parentUrl . '#', $request->getHeaderLine('Referer'))
        ) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', $parentUrl);
        }

        $view = new PageView();
        $view->setRouter($this->router);

        return new HtmlResponse(
            $this->template->render('contact::thankyou', $view)
        );
    }
}
