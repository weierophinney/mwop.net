<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App\Factory;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Swift_AWSTransport as AWSTransport;
use Swift_Mailer as Mailer;
use Swift_SmtpTransport as SMTPTransport;

use function sprintf;

class MailTransport
{
    public function __invoke(ContainerInterface $container): Mailer
    {
        $config = $container->get('config-mail.transport');
        $class  = $config['class'] ?? SMTPTransport::class;

        switch ($class) {
            case AWSTransport::class:
                $transport = new $class($config['username'], $config['password']);
                break;
            case SMTPTransport::class:
                $transport = $config['ssl']
                    ? new $class($config['host'], $config['port'], $config['ssl'])
                    : new $class($config['host'], $config['port']);

                if ($config['username']) {
                    $transport->setUsername($config['username']);
                    $transport->setAuthMode('login');
                }

                if ($config['password']) {
                    $transport->setPassword($config['password']);
                }
                break;
            default:
                throw new RuntimeException(sprintf('Unknown mail transport class %s', $class));
        }

        return new Mailer($transport);
    }
}
