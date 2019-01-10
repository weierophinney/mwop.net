<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\TaskWorker;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Swoole\Http\Server as HttpServer;

class QueueableListenerProvider extends AttachableListenerProvider
{
    /** @var HttpServer */
    private $server;

    public function __construct(HttpServer $server)
    {
        $this->server = $server;
    }

    /**
     * {@inheritDocs}
     *
     * If $listener implements ListenerShouldQueue, this method will decorate
     * the listener in a QueuableListener instance before calling the parent
     * to attach the listener.
     */
    public function listen(string $eventName, callable $listener) : void
    {
        if ($listener instanceof ListenerShouldQueue) {
            $listener = new QueueableListener($this->server, $listener);
        }

        parent::listen($eventName, $listener);
    }
}
