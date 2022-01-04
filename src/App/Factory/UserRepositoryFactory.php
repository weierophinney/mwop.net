<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use InvalidArgumentException;
use Mezzio\Authentication\DefaultUser;
use Mezzio\Authentication\UserInterface;
use Mezzio\Authentication\UserRepositoryInterface;
use Psr\Container\ContainerInterface;

class UserRepositoryFactory
{
    public function __invoke(ContainerInterface $container): UserRepositoryInterface
    {
        $config = $container->get('config-authentication')['allowed_credentials'];
        return new class ($config['username'], $config['password']) implements UserRepositoryInterface {
            private string $username;
            private string $password;

            public function __construct(?string $username, ?string $password)
            {
                if (
                    null === $username
                    || null === $password
                    || empty($username)
                    || empty($password)
                ) {
                    throw new InvalidArgumentException('Empty or missing username or password for user repository');
                }

                $this->username = $username;
                $this->password = $password;
            }

            public function authenticate(string $credential, ?string $password = null): ?UserInterface
            {
                if (
                    $credential !== $this->username
                    || $password !== $this->password
                ) {
                    return null;
                }

                return new DefaultUser($this->username, ['admin']);
            }
        };
    }
}
