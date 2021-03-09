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

use function error_log;
use function file_exists;
use function is_array;
use function sprintf;
use function var_export;

class HomePageHandler implements RequestHandlerInterface
{
    public const TEMPLATE = 'mwop::home.page';

    public function __construct(
        private array $posts,
        private string $instagramFeedLocation,
        private TemplateRendererInterface $renderer
    ) {
    }

    public function handle(Request $request): Response
    {
        return new HtmlResponse(
            $this->renderer->render(self::TEMPLATE, [
                'posts'     => $this->posts,
                'instagram' => $this->getInstagramPosts(),
            ])
        );
    }

    public function getInstagramPosts(): array
    {
        if (empty($this->instagramFeedLocation) || ! file_exists($this->instagramFeedLocation)) {
            error_log(sprintf(
                'Instagram feed location "%s" does not exist',
                var_export($this->instagramFeedLocation, true)
            ));
            return [];
        }

        $posts = include $this->instagramFeedLocation;

        if (! is_array($posts)) {
            error_log(sprintf('Failed to fetch instagram feed from %s', $this->instagramFeedLocation));
            return [];
        }

        return $posts;
    }
}
