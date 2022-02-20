<?php

declare(strict_types=1);

namespace Mwop\App;

use Mezzio\Swoole\Event\ServerStartEvent;
use Swoole\Runtime;

use const SWOOLE_HOOK_NATIVE_CURL;

class ServerStartListener
{
    public function __invoke(ServerStartEvent $event): void
    {
        Runtime::enableCoroutine(SWOOLE_HOOK_NATIVE_CURL);
    }
}
