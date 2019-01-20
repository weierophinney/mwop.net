<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

use League\Plates\Engine;
use Mwop\Factory\PlatesFunctionsDelegator;
use Zend\Expressive\Plates\PlatesEngineFactory;

return [
    'dependencies' => [
        'delegators' => [
            Engine::class => [
                PlatesFunctionsDelegator::class,
            ],
        ],
        'factories' => [
            Engine::class => PlatesEngineFactory::class,
        ],
    ],
    'templates' => [
        'extension' => 'phtml',
        'paths' => [
            'data'    => [getcwd() . '/data'],
            'error'   => ['templates/error'],
            'layout'  => ['templates/layout'],
            'mwop'    => ['templates/mwop'],
        ],
    ],
];
