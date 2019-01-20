<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\OAuth2;

class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'oauth'        => [],
            'debug'        => false,
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplateConfig(),
        ];
    }

    public function getDependencies() : array
    {
        // phpcs:disable
        return [
            'factories' => [
                Provider\ProviderFactory::class                            => Provider\ProviderFactoryFactory::class,
                Handler\CallbackHandler::class                             => Handler\CallbackHandlerFactory::class,
                Middleware\Middleware\CheckAuthenticationMiddleware::class => CheckAuthenticationMiddlewareFactory::class,
                Handler\RequestAuthenticationHandler::class                => Handler\RequestAuthenticationHandlerFactory::class,
            ],
        ];
        // phpcs:enable
    }

    public function getTemplateConfig() : array
    {
        return [
            'paths' => [
                'oauth2' => [__DIR__ . '/templates'],
            ],
        ];
    }
}
