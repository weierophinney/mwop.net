<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact


declare(strict_types=1);

namespace Mwop\Contact;

use JsonSerializable;

class SendContactMessageEvent implements JsonSerializable
{
    public function __construct(
        private string $replyTo,
        private string $subject,
        private string $body,
    ) {
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function jsonSerialize(): array
    {
        return [
            'replyTo' => $this->replyTo,
            'subject' => $this->subject,
            'body'    => $this->body,
        ];
    }
}
