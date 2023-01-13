<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Webmozart\Assert\Assert;

use function sprintf;

final class Media
{
    /** @var resource */
    public $stream;

    /** @param resource $stream */
    public function __construct(
        $stream,
        public readonly string $filename,
        public readonly string $contentType,
    ) {
        Assert::resource($stream, message: sprintf('%s did not receive a resource for the stream value', self::class));
        $this->stream = $stream;
    }

    /** @return resource */
    public function getStream()
    {
        return $this->stream;
    }
}
