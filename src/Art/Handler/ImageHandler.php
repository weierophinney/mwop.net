<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Mezzio\Router\RouteResult;
use Mwop\Art\PhotoStorage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ImageHandler implements RequestHandlerInterface
{
    public function __construct(
        private PhotoStorage $photos,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);
        $matches     = $routeResult->getMatchedParams();

        $type = match ($matches['type']) {
            'fullsize'   => PhotoStorage::TYPE_IMAGE,
            'thumbnails' => PhotoStorage::TYPE_THUMBNAIL,
        };

        return $this->photos->getPsr7ResponseForImageName($matches['image'], $type);
    }
}
