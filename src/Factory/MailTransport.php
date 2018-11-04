<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Swift_Mailer as Mailer;
use Swift_SmtpTransport as Transport;

class MailTransport
{
    public function __invoke(ContainerInterface $container) : Mailer
    {
        $config  = $container->get('config');
        $config  = $config['mail']['transport'];
        $class   = $config['class'] ?? \Swift_SmtpTransport::class;

        switch ($class) {
            case \Swift_AWSTransport::class:
                $transport = new $class($config['username'], $config['password']);
                break;
            case \Swift_SmtpTransport::class:
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
