<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\TaskWorker;

final class Task
{
    /**
     * @var object
     */
    private $event;

    /**
     * @var callable
     */
    private $listener;

    public function __construct(object $event, callable $listener)
    {
        $this->event = $event;
        $this->listener = $listener;
    }

    public function event() : object
    {
        return $this->event;
    }

    public function listener() : callable
    {
        return $this->listener;
    }
}
