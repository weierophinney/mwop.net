<?php

declare(strict_types=1);

namespace Mwop\Hooks;

use Mezzio\Application;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

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
            'delegators' => [
                AttachableListenerProvider::class => [
                    WebhookPayloadListenerDelegator::class,
                ],
                WebhookPayloadListener::class     => [
                    DeferredServiceListenerDelegator::class,
                ],
            ],
            'factories'  => [
                'config-hooks'                                     => ConfigFactory::class,
                Handler\GitHubAtomHandler::class                   => Handler\GitHubAtomHandlerFactory::class,
                Middleware\ValidateWebhookRequestMiddleware::class => Middleware\ValidateWebhookRequestMiddlewareFactory::class,
                WebhookPayloadListener::class                      => WebhookPayloadListenerFactory::class,
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

    public function registerRoutes(Application $app, string $basePath = '/api/hook'): void
    {
        $app->post($basePath . '/github', [
            ProblemDetailsMiddleware::class,
            Middleware\ValidateWebhookRequestMiddleware::class,
            Handler\GitHubAtomHandler::class,
        ], 'api.hook.github');
    }
}
