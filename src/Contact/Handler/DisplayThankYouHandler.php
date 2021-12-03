<?php

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
    public function __construct(private TemplateRendererInterface $template, private UrlHelper $urlHelper)
    {
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
