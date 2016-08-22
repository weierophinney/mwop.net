<?php
namespace Mwop\Contact;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class ThankYouPage
{
    private $template;

    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
    }

    public function __invoke(Request $request, Response $response, callable $next) : Response
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
            $this->template->render('contact::thankyou', [])
        );
    }
}
