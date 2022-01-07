<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols,Generic.WhiteSpace.ScopeIndent.IncorrectExact


declare(strict_types=1);

namespace Mwop\App\Factory;

use Psr\Container\ContainerInterface;
use SendGrid;

class MailTransport
{
    public function __invoke(ContainerInterface $container): SendGrid
    {
        $config = $container->get('config-mail.transport');
        return new SendGrid($config['apikey']);
    }
}
