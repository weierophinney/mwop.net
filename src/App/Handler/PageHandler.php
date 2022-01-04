<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact

declare(strict_types=1);

namespace Mwop\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class PageHandler implements RequestHandlerInterface
{
    public function __construct(
        private string $page,
        private TemplateRendererInterface $template
    ) {
    }

    public function handle(Request $request): Response
    {
        return new HtmlResponse(
            $this->template->render($this->page, [])
        );
    }
}
