<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

use Psr\Log\LoggerInterface;
use SendGrid;
use SendGrid\Mail\Mail;
use Throwable;

use function array_map;
use function implode;
use function json_decode;
use function sprintf;

class ErrorNotifier
{
    public function __construct(
        private SendGrid $mailer,
        private LoggerInterface $logger,
        private string $sender,
        private string $recipient,
    ) {
    }

    public function sendNotification(string $content): void
    {
        $message = new Mail();
        $message->setFrom($this->sender);
        $message->addTo($this->recipient);
        $message->setSubject('[mwop.net] Failure to process new photo');
        $message->addContent('text/plain', $content);

        try {
            $response = $this->mailer->send($message);
        } catch (Throwable $e) {
            if ($this->logger) {
                $this->logger->warning(sprintf(
                    'Exception thrown sending photo failure notification email (%s): %s',
                    $e::class,
                    $e->getMessage(),
                ));
                return;
            }
        }

        if ($response->statusCode() >= 400) {
            $errors        = json_decode($response->getBody())->errors;
            $errorMessages = array_map(fn (object $error): string => $error->message, $errors);

            $this->logger->warning(sprintf(
                'Error sending photo failure notification email: %s',
                implode("\n", $errorMessages),
            ));
        }
    }
}
