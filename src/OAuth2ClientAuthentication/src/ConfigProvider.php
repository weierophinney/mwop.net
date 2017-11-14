<?php

namespace Phly\OAuth2ClientAuthentication;

use Zend\Expressive\Authentication\AuthenticationInterface;

/**
 * The configuration provider for the OAuth2ClientAuthentication module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'aliases' => [
                AuthenticationInterface::class => OAuth2Adapter::class,
            ],
            'factories'  => [
                OAuth2Adapter::class => OAuth2AdapterFactory::class,
                OAuth2CallbackMiddleware::class => OAuth2CallbackMiddlewareFactory::class,
                OAuth2ProviderFactory::class => OAuth2ProviderFactoryFactory::class,
                RedirectResponseFactory::class => RedirectResponseFactoryFactory::class,
                UnauthorizedResponseFactory::class => UnauthorizedResponseFactoryFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array
     */
    public function getTemplates()
    {
        return [
            'paths' => [
                'oauth2clientauthentication' => [__DIR__ . '/../templates'],
            ],
        ];
    }
}
