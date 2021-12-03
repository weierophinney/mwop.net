<?php

declare(strict_types=1);

namespace Mwop\Contact\Listener;

use Mwop\Contact\SendContactMessageEvent;
use Swift_Mailer as Mailer;
use Swift_Message as MailMessage;

class SendContactMessageListener
{
    public function __construct(private Mailer $mailer, private array $config = [])
    {
    }

    public function __invoke(SendContactMessageEvent $contactMessage)
    {
        $message = $this->createMessage();
        $message
            ->setReplyTo($contactMessage->getReplyTo())
            ->setSubject($contactMessage->getSubject())
            ->setBody($contactMessage->getBody());
        $this->mailer->send($message);
    }

    private function createMessage(): MailMessage
    {
        $message = new MailMessage();
        $message->setTo($this->config['to']);
        $message->setFrom($this->config['sender']['address']);
        $message->setSender($this->config['sender']['address']);
        return $message;
    }
}
