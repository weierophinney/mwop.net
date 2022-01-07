<?php

declare(strict_types=1);

namespace Mwop\Contact\Listener;

use Mwop\Contact\SendContactMessageEvent;
use Psr\Log\LoggerInterface;
use SendGrid;
use SendGrid\Mail\Mail;
use Throwable;

use function array_map;
use function implode;
use function json_decode;
use function sprintf;

class SendContactMessageListener
{
    public function __construct(
        private SendGrid $mailer,
        private array $config = [],
        private ?LoggerInterface $logger = null
    ) {
    }

    public function __invoke(SendContactMessageEvent $contactMessage)
    {
        $message = $this->createMessage();
        $message->setReplyTo($contactMessage->getReplyTo());
        $message->setSubject($contactMessage->getSubject());
        $message->addContent(
            'text/plain',
            $contactMessage->getBody()
        );

        try {
            $response = $this->mailer->send($message);
        } catch (Throwable $e) {
            if ($this->logger) {
                $this->logger->warning(sprintf(
                    'Exception thrown sending email (%s): %s',
                    $e::class,
                    $e->getMessage(),
                ));
                return;
            }
        }

        if ($response->statusCode() >= 400 && $this->logger !== null) {
            $errors        = json_decode($response->getBody())->errors;
            $errorMessages = array_map(fn (stdClass $error): string => $error->message, $errors);

            $this->logger->warning(sprintf(
                'Error sending contact email: %s',
                implode("\n", $errorMessages)
            ));
        }
    }

    private function createMessage(): Mail
    {
        $message = new Mail();
        $message->setFrom($this->config['sender']['address'], $this->config['sender']['name']);
        $message->addTo($this->config['to']);
        return $message;
    }
}
