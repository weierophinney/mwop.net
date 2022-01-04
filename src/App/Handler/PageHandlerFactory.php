<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function array_pop;
use function array_shift;
use function explode;
use function preg_replace;
use function preg_replace_callback;
use function sprintf;
use function strlen;
use function strtolower;

class PageHandlerFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName): PageHandler
    {
        return new PageHandler(
            $this->deriveTemplateName($requestedName),
            $container->get(TemplateRendererInterface::class)
        );
    }

    private function deriveTemplateName(string $service): string
    {
        $parts = explode('\\', $service);
        $ns    = array_shift($parts);
        $page  = array_pop($parts);
        $page  = preg_replace('/Handler$/', '', $page);

        return sprintf(
            '%s::%s',
            $this->camelCaseToDotSeparated($ns),
            $this->camelCaseToDotSeparated($page)
        );
    }

    private function camelCaseToDotSeparated(string $string): string
    {
        return preg_replace_callback(
            '/(^|[a-z])([A-Z])/',
            fn (array $matches): string => strtolower(
                strlen($matches[1])
                    ? sprintf('%s.%s', $matches[1], $matches[2])
                    : $matches[2]
            ),
            $string
        );
    }
}
