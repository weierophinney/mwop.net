<?php

declare(strict_types=1);

namespace Mwop\Contact;

use Mezzio\Application;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplateConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                Handler\ContactPageHandler::class => Handler\ContactPageHandlerFactory::class,
            ],
        ];
    }

    public function getTemplateConfig(): array
    {
        return [
            'paths' => [
                'contact' => [__DIR__ . '/templates'],
            ],
        ];
    }

    public function registerRoutes(Application $app, string $basePath = '/contact'): void
    {
        $app->get($basePath . '[/]', [
            Handler\ContactPageHandler::class,
        ], 'contact');
    }
}
