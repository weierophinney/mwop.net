<?php

declare(strict_types=1);

namespace Mwop\Hooks;

use Phly\ConfigFactory\ConfigFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'hooks'        => $this->getHooksConfig(),
        ];
    }

    public function getDependencies(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'factories' => [
                'config-hooks'                                     => ConfigFactory::class,
                Middleware\ValidateWebhookRequestMiddleware::class => Middleware\ValidateWebhookRequestMiddlewareFactory::class,
            ],
        ];
        // phpcs:enable Generic.Files.LineLength.TooLong
    }

    public function getHooksConfig(): array
    {
        return [
            'token-header' => 'X-MWOPNET-HOOK-TOKEN',
            'token-value'  => '',
        ];
    }
}
