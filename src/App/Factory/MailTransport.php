<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols,Generic.WhiteSpace.ScopeIndent.IncorrectExact

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

        $transport = match($class) {
            AWSTransport::class  => new $class($config['username'], $config['password']),
            SMTPTransport::class => $this->smtpTransportFactory($class, $config),
            // phpcs:ignore
            default              => throw new RuntimeException(sprintf('Unknown mail transport class %s', $class)),
        };

        return new Mailer($transport);
    }

    private function smtpTransportFactory(string $class, array $config): SMTPTransport
    {
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

        return $transport;
    }
}
