<?php

declare(strict_types=1);

namespace Mwop\ActivityPub\Webfinger;

use Mezzio\Hal\Link;
use Mezzio\Hal\LinkCollection;
use Psr\Link\LinkInterface;

class Matthew implements Account
{
    use LinkCollection;

    private array $aliases = [
        'https://mwop.net',
        'https://phpc.social/@mwop',
        'https://phpc.social/users/mwop',
        // 'https://pixelfed.social/@mwop',
        // 'https://pixelfed.social/users/mwop',
    ];

    private string $subject = 'acct:matthew@mwop.net';

    public function __construct()
    {
        $this->links = [
            $this->createProfileLink(),
            ...$this->createPhpSocialLinks(),
            // ...$this->createPixelfedLinks(),
        ];
    }

    public function getAliases(): iterable
    {
        return $this->aliases;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function jsonSerialize(): array
    {
        return [
            'subject' => $this->subject,
            'aliases' => $this->aliases,
            'links'   => LinkSerializer::serializeCollection($this->links),
        ];
    }

    private function createProfileLink(): LinkInterface
    {
        return new Link('http://webfinger.net/rel/profile-page', 'https://mwop.net/', false, [
            'type' => 'text/html',
        ]);
    }

    /** @psalm-return list<LinkInterface> */
    private function createPhpSocialLinks(): array
    {
        return [
            new Link('self', 'https://phpc.social/users/mwop', false, [
                'type' => 'application/activity+json',
            ]),
            new Link(
                'http://ostatus.org/schema/1.0/subscribe',
                'https://phpc.social/authorize_interaction?uri={uri}',
                true
            ),
        ];
    }

    /** @psalm-return list<LinkInterface> */
    private function createPixelfedLinks(): array
    {
        return [
            new Link('self', 'https://pixelfed.social/users/mwop', false, [
                'type' => 'application/activity+json',
            ]),
            new Link(
                'http://schemas.google.com/g/2010#updates-from',
                'https://pixelfed.social/users/mwop.atom',
                false,
                ['type' => 'application/atom+xml'],
            ),
        ];
    }
}
