<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Contact;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class ThankYouPage implements RequestHandlerInterface
{
    private $template;

    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
    }

    public function handle(Request $request) : Response
    {
        $parent    = $request->getAttribute('originalRequest', $request);
        $parentUrl = str_replace('/thank-you', '', (string) $parent->getUri());
        if (! $request->hasHeader('Referer')
            || ! preg_match('#^' . $parentUrl . '#', $request->getHeaderLine('Referer'))
        ) {
            return new RedirectResponse($parentUrl);
        }

        return new HtmlResponse(
            $this->template->render('contact::thankyou', [])
        );
    }
}
