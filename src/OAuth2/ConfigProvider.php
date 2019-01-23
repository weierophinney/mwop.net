<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\OAuth2;

use Zend\Expressive\Application;
use Zend\Expressive\Session\SessionMiddleware;

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
                Handler\CallbackHandler::class                  => Handler\CallbackHandlerFactory::class,
                Handler\RequestAuthenticationHandler::class     => Handler\RequestAuthenticationHandlerFactory::class,
                Middleware\CheckAuthenticationMiddleware::class => Middleware\CheckAuthenticationMiddlewareFactory::class,
                Provider\ProviderFactory::class                 => Provider\ProviderFactoryFactory::class,
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

    public function registerRoutes(string $basePath, Application $app) : void
    {
        // OAuth2 authentication response
        $app->get($basePath . '/{provider:debug|github|google}/oauth2callback', [
            SessionMiddleware::class,
            Handler\CallbackHandler::class,
        ]);

        // OAuth2 authentication request
        $app->get($basePath . '/{provider:debug|github|google}', [
            SessionMiddleware::class,
            Handler\RequestAuthenticationHandler::class,
        ]);
    }
}
