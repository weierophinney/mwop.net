<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\EventDispatcher\ListenerProviderInterface;
use Zend\Expressive\Session\Cache\CacheSessionPersistence;
use Zend\Expressive\Session\SessionPersistenceInterface;

return ['dependencies' => [
    'aliases' => [
        ListenerProviderInterface::class   => AttachableListenerProvider::class,
        SessionPersistenceInterface::class => CacheSessionPersistence::class,
    ],
    'invokables' => [
    ],
    'factories' => [
    ],
]];
