<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Contact;

use JsonSerializable;

class SendContactMessageEvent implements JsonSerializable
{
    /** @var string */
    private $body;

    /** @var string */
    private $replyTo;

    /** @var string */
    private $subject;

    public function __construct(
        string $replyTo,
        string $subject,
        string $body
    ) {
        $this->replyTo = $replyTo;
        $this->subject = $subject;
        $this->body    = $body;
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
