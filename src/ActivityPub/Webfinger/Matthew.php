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
        'https://phpc.social/@mwop',
        'https://phpc.social/users/mwop',
    ];

    private string $subject = 'acct:matthew@mwop.net';

    public function __construct()
    {
        $this->links = [
            $this->createProfileLink(),
            $this->createSelfLink(),
            $this->createSubscribeLink(),
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

    private function createSelfLink(): LinkInterface
    {
        return new Link('self', 'https://phpc.social/users/mwop', false, [
            'type' => 'application/activity+json',
        ]);
    }

    private function createSubscribeLink(): LinkInterface
    {
        return new Link(
            'http://ostatus.org/schema/1.0/subscribe',
            'https://phpc.social/authorize_interaction?uri={uri}',
            true
        );
    }
}
