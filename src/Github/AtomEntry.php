<?php

declare(strict_types=1);

namespace Mwop\Github;

use JsonSerializable;
use Stringable;

use function preg_match;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class AtomEntry implements JsonSerializable, Stringable
{
    private const TEMPLATE = '<li><a href="%s">%s</a></li>';

    /** @var null|non-empty-string */
    private readonly ?string $link;

    /** @param non-empty-string $link */
    public function __construct(
        string $link,
        /** @var non-empty-string */
        public readonly string $title,
        /** @var non-empty-string */
        public readonly string $content,
    ) {
        $this->link = preg_match('#weierophinney/#', $link) ? null : $link;
    }

    public function __toString(): string
    {
        return sprintf(self::TEMPLATE, $this->link, $this->title);
    }

    public function jsonSerialize(): array
    {
        return [
            'link'    => $this->link,
            'title'   => $this->title,
            'content' => $this->content,
        ];
    }
}
