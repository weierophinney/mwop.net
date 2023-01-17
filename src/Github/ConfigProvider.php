<?php

declare(strict_types=1);

namespace Mwop\Github;

use League\Plates\Engine;
use Mezzio\Application;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Mwop\Hooks\Middleware\ValidateWebhookRequestMiddleware;
use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

use function getcwd;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'github'       => $this->getConfig(),
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getConfig(): array
    {
        return [
            'user'      => '',
            'limit'     => 10,
            'list_file' => getcwd() . '/data/github-feed.json',
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class                => [
                    RoutesDelegator::class,
                ],
                AttachableListenerProvider::class => [
                    Webhook\PayloadListenerDelegator::class,
                ],
                Engine::class                     => [
                    RenderLinksDelegator::class,
                ],
                Webhook\PayloadListener::class    => [
                    DeferredServiceListenerDelegator::class,
                ],
            ],
            'factories'  => [
                AtomReader::class              => AtomReaderFactory::class,
                'config-github'                => ConfigFactory::class,
                Console\Fetch::class           => Console\FetchFactory::class,
                Handler\AtomHandler::class     => Handler\AtomHandlerFactory::class,
                ItemList::class                => ItemListFactory::class,
                Webhook\PayloadListener::class => Webhook\PayloadListenerFactory::class,
            ],
        ];
    }
}
