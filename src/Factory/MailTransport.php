<?php
namespace Mwop\Factory;

use Zend\Mail\Transport;

class MailTransport
{
    public function __invoke($services)
    {
        $config  = $services->get('Config');
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
