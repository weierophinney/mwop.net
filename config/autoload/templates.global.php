<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

use Mwop\Factory\UriTemplateDelegatorFactory;
use Phly\Expressive\Mustache\MustacheTemplateFactory;
use Phly\Mustache\Pragma;
use Zend\Expressive\Template\TemplateRendererInterface;

return [
    'dependencies' => [
        'delegators' => [
            TemplateRendererInterface::class => [
                UriTemplateDelegatorFactory::class,
            ],
        ],
        'factories' => [
            TemplateRendererInterface::class => MustacheTemplateFactory::class,
        ],
    ],

    'phly-mustache' => [
        'paths' => [
            'blog'    => getcwd() . '/templates/blog',
            'contact' => getcwd() . '/templates/contact',
            'error'   => getcwd() . '/templates/error',
            'layout'  => getcwd() . '/templates/layout',
            'oauth2clientauthentication' => getcwd() . '/src/OAuth2ClientAuthentication/templates',
            'mwop'    => getcwd() . '/templates/mwop',
            [
                getcwd() . '/templates',
                getcwd() . '/data',
            ],
        ],
        'pragmas' => [
            Pragma\ContextualEscape::class,
            Pragma\ImplicitIterator::class,
        ],
    ],

    'templates' => [
        'extension' => 'mustache',
        'paths' => [
            'blog'    => ['templates/blog'],
            'contact' => ['templates/contact'],
            'error'   => ['templates/error'],
            'layout'  => ['templates/layout'],
            'mwop'    => ['templates/mwop'],
            [
                getcwd() . '/templates',
                getcwd() . '/data',
            ],
        ],
    ],
];
