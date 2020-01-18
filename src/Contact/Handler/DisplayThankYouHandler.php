<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Contact\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

use function preg_match;

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

    public function handle(Request $request): Response
    {
        $parentUrl = $this->urlHelper->generate('contact');
        if (
            ! $request->hasHeader('Referer')
            || ! preg_match('#^(https?://[^/]+)' . $parentUrl . '#', $request->getHeaderLine('Referer'))
        ) {
            return new RedirectResponse($parentUrl);
        }

        return new HtmlResponse(
            $this->template->render('contact::thankyou', [])
        );
    }
}
