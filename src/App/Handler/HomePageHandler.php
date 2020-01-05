<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\App\Handler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class HomePageHandler implements RequestHandlerInterface
{
    const TEMPLATE = 'mwop::home.page';

    private $instagramFeedLocation;
    private $posts;
    private $renderer;

    public function __construct(
        array $posts,
        string $instagramFeedLocation,
        TemplateRendererInterface $renderer
    ) {
        $this->posts                 = $posts;
        $this->instagramFeedLocation = $instagramFeedLocation;
        $this->renderer              = $renderer;
    }

    public function handle(Request $request) : Response
    {
        return new HtmlResponse(
            $this->renderer->render(self::TEMPLATE, [
                'posts'     => $this->posts,
                'instagram' => $this->getInstagramPosts(),
            ])
        );
    }

    public function getInstagramPosts() : array
    {
        if (empty($this->instagramFeedLocation) || ! file_exists($this->instagramFeedLocation)) {
            return [];
        }

        $posts = include $this->instagramFeedLocation;
        return $posts ?: [];
    }
}
