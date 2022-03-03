<?php

declare(strict_types=1);

namespace Mwop\Now;

use League\Plates\Engine;
use Mezzio\Application;
use Phly\ConfigFactory\ConfigFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'now-and-then' => $this->getConfig(),
            'templates'    => $this->getTemplateConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Engine::class => [
                    MarkdownPlatesDelegator::class,
                ],
            ],
            'factories'  => [
                'config-now-and-then'       => ConfigFactory::class,
                Archives::class             => ArchivesFactory::class,
                Handler\PageHandler::class  => Handler\PageHandlerFactory::class,
                NowAndThenFilesystem::class => NowAndThenFilesystemFactory::class,
            ],
        ];
    }

    public function getConfig(): array
    {
        return [
            'storage' => [
                'folder' => 'now-and-then',
            ],
        ];
    }

    public function getTemplateConfig(): array
    {
        return [
            'paths' => [
                'now' => [__DIR__ . '/templates'],
            ],
        ];
    }

    public function registerRoutes(Application $app): void
    {
        $app->get('/now', Handler\PageHandler::class, 'now');
        $app->get('/then/{when:\d{4}-\d{2}}', Handler\PageHandler::class, 'now.then');
    }
}
