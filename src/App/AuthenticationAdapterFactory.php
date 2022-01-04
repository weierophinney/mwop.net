<?php

declare(strict_types=1);

namespace Mwop\App;

use Mezzio\Authentication\Session\PhpSession;
use Psr\Container\ContainerInterface;

class AuthenticationAdapterFactory
{
    public function __invoke(ContainerInterface $container): AuthenticationAdapter
    {
        return new AuthenticationAdapter($container->get(PhpSession::class));
    }
}
