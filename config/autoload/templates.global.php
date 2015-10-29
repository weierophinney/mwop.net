<?php

use Phly\Expressive\Mustache\MustacheTemplateFactory;
use Phly\Mustache\Pragma;
use Zend\Expressive\Container\TemplatedErrorHandlerFactory;
use Zend\Expressive\Template\TemplateRendererInterface;

return [
    'dependencies' => [
        'factories' => [
            'Zend\Expressive\FinalHandler'   => TemplatedErrorHandlerFactory::class,
            TemplateRendererInterface::class => MustacheTemplateFactory::class,
        ],
    ],

    'phly-mustache' => [
        'paths' => [
            'blog'    => getcwd() . '/templates/blog',
            'contact' => getcwd() . '/templates/contact',
            'error'   => getcwd() . '/templates/error',
            'layout'  => getcwd() . '/templates/layout',
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
