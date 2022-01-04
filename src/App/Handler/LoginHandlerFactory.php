<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class LoginHandlerFactory
{
    public function __invoke(ContainerInterface $container): LoginHandler
    {
        return new LoginHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(AuthenticationInterface::class),
        );
    }
}
