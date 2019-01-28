<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact\Handler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class DisplayThankYouHandler implements RequestHandlerInterface
{
    /** @var TemplateRendererInterface */
    private $template;

    /** @var UrlHelper */
    private $urlHelper;

    public function __construct(TemplateRendererInterface $template, UrlHelper $urlHelper)
    {
        $this->template  = $template;
        $this->urlHelper = $urlHelper;
    }

    public function handle(Request $request) : Response
    {
        $parentUrl = $this->urlHelper->generate('contact');
        if (! $request->hasHeader('Referer')
            || ! preg_match('#^(https?://[^/]+)' . $parentUrl . '#', $request->getHeaderLine('Referer'))
        ) {
            return new RedirectResponse($parentUrl);
        }

        return new HtmlResponse(
            $this->template->render('contact::thankyou', [])
        );
    }
}
