<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Psr\Http\Message\StreamInterface;
use Webmozart\Assert\Assert;

use function json_decode;

use const JSON_THROW_ON_ERROR;

final class Authorization
{
    private function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType,
    ) {
    }

    public static function fromAuthenticationPayload(StreamInterface $stream): self
    {
        $json   = $stream->getContents();
        $values = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        // phpcs:disable Generic.Files.LineLength.TooLong
        Assert::isMap($values, 'Unexpected value returned for Mastodon API authentication payload');
        Assert::keyExists($values, 'access_token', 'Mastodon API authenticationpayload missing access_token');
        Assert::string($values['access_token'], 'Non-string access_token returned in Mastodon API authentication payload');
        Assert::keyExists($values, 'token_type', 'Mastodon API authenticationpayload missing token_type');
        Assert::string($values['token_type'], 'Non-string token_type returned in Mastodon API authentication payload');
        // phpcs:enable Generic.Files.LineLength.TooLong

        return new self($values['access_token'], $values['token_type']);
    }
}
