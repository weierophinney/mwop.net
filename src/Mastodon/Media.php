<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Webmozart\Assert\Assert;

use function sprintf;

final class Media
{
    /** @var resource|StreamInterface */
    public $stream;

    /** @param resource|StreamInterface $stream */
    public function __construct(
        $stream,
        public readonly string $filename,
        public readonly string $contentType,
    ) {
        if (! is_resource($stream) && ! $stream instanceof StreamInterface) {
            throw new InvalidArgumentException(sprintf(
                'Media stream MUST be a resource or a PSR-7 StreamInterface; received %s',
                get_debug_type($stream)
            ));
        }
        $this->stream = $stream;
    }

    /** @return resource|StreamInterface */
    public function getStream()
    {
        return $this->stream;
    }
}
