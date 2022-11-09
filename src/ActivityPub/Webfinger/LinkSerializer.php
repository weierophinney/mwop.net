<?php

declare(strict_types=1);

namespace Mwop\ActivityPub\Webfinger;

use Psr\Link\LinkInterface;

use function array_map;
use function array_shift;
use function count;

final class LinkSerializer
{
    /**
     * @param LinkInterface[] $links
     * @psalm-return list<array{href: string, rel: string, type: string}|array{rel: string, template: string}>
     */
    public static function serializeCollection(array $links): array
    {
        return array_map(fn (LinkInterface $link): array => self::serializeLink($link), $links);
    }

    /**
     * @psalm-return array{href: string, rel: string, type: string}|array{rel: string, template: string}
     */
    public static function serializeLink(LinkInterface $link): array
    {
        if ($link->isTemplated()) {
            return [
                'rel'      => self::getRelValue($link),
                'template' => $link->getHref(),
            ];
        }

        $attributes = $link->getAttributes();

        return [
            'rel'  => self::getRelValue($link),
            'href' => $link->getHref(),
            ...$attributes,
        ];
    }

    private static function getRelValue(LinkInterface $link): string|array
    {
        $rels = $link->getRels();
        if (count($rels) > 1) {
            return $rels;
        }
        return array_shift($rels);
    }

    private function __construct()
    {
    }
}
