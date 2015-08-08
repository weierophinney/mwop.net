<?php
namespace Mwop\Contact;

use Mwop\Template\TemplateInterface;
use Zend\Diactoros\Response\HtmlResponse;

class ThankYouPage
{
    private $template;

    public function __construct(TemplateInterface $template)
    {
        $this->template = $template;
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

        return new HtmlResponse(
            $this->template->render('contact.thankyou')
        );
    }
}
