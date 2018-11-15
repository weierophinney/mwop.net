<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact;

use Swift_Mailer as Mailer;
use Swift_Message as MailMessage;

class SendMessageListener
{
    /** @var array */
    private $config;

    /** @var Mailer */
    private $mailer;

    public function __construct(Mailer $mailer, array $config = [])
    {
        $this->mailer = $mailer;
        $this->config = $config;
    }

    public function __invoke(ContactMessage $contactMessage)
    {
        $message = $this->createMessage();
        $message
            ->setReplyTo($contactMessage->getReplyTo())
            ->setSubject($contactMessage->getSubject())
            ->setBody($contactMessage->getBody());
        $this->mailer->send($message);
    }

    private function createMessage() : MailMessage
    {
        $message = new MailMessage();
        $message->setTo($this->config['to']);
        $message->setFrom($this->config['sender']['address']);
        $message->setSender($this->config['sender']['address']);
        return $message;
    }
}
