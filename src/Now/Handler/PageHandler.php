<?php

declare(strict_types=1);

namespace Mwop\Now\Handler;

use Illuminate\Support\Collection;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function basename;

class PageHandler implements RequestHandlerInterface
{
    public function __construct(
        private Collection $archives,
        private ResponseFactoryInterface $responseFactory,
        private TemplateRendererInterface $renderer,
        private FilesystemOperator $fs,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routing */
        $routing = $request->getAttribute(RouteResult::class);
        $when    = $routing->getMatchedParams()['when'] ?? null;
        $page    = $this->getPage($when);

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            $this->renderer->render('now::page', [
                'fs'       => $this->fs,
                'latest'   => $when === null,
                'page'     => $page,
                'archives' => $this->archives,
            ])
        );

        return $response;
    }

    private function getPage(?string $when): StorageAttributes
    {
        if (null === $when) {
            return $this->archives->first();
        }

        return $this->archives->reduce(
            function (?StorageAttributes $found, StorageAttributes $file) use ($when): ?StorageAttributes {
                if ($found) {
                    return $found;
                }

                $filename = basename($file->path(), '.md');

                if ($filename === $when) {
                    return $file;
                }

                if ($filename < $when) {
                    return $file;
                }

                return null;
            },
            null,
        );
    }
}
