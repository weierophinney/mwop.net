<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    public const TEMPLATE = 'mwop::home.page';

    public function __construct(
        private array $posts,
        private TemplateRendererInterface $renderer
    ) {
    }

    public function handle(Request $request): Response
    {
        return new HtmlResponse(
            $this->renderer->render(self::TEMPLATE, [
                'posts' => $this->posts,
            ])
        );
    }
}
