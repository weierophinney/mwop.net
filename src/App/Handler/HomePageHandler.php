<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\Art\PhotoMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    public const TEMPLATE = 'mwop::home.page';

    public function __construct(
        private PhotoMapper $photos,
        private TemplateRendererInterface $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $photos = $this->photos->fetchAll();
        $photos->setItemCountPerPage(12);
        $photos->setCurrentPageNumber(1);

        return new HtmlResponse(
            $this->renderer->render(self::TEMPLATE, [
                'photos' => $photos,
            ])
        );
    }
}
