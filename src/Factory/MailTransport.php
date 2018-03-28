<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Zend\Mail\Transport;

class MailTransport
{
    public function __invoke(ContainerInterface $container) : Transport\TransportInterface
    {
        $config  = $container->get('config');
        $config  = $config['mail']['transport'];
        $class   = $config['class'];
        $options = $config['options'];

        switch ($class) {
            case 'Zend\Mail\Transport\SendMail':
            case 'Sendmail':
            case 'sendmail':
                return new Transport\Sendmail;
            case 'Zend\Mail\Transport\Smtp':
            case 'Smtp':
            case 'smtp':
                $options = new Transport\SmtpOptions($options);
                return new Transport\Smtp($options);
            case 'Zend\Mail\Transport\File':
            case 'File':
            case 'file':
                $options = new Transport\FileOptions($options);
                return new Transport\File($options);
            default:
                throw new RuntimeException(sprintf(
                    'Unknown mail transport type "%s"',
                    $class
                ));
        }
    }
}
