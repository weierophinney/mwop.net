<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\TaskWorker;

/**
 * Marker interface indicating listener should be queued.
 *
 * A listener provider can introspect listeners that implement this
 * interface, and decorate them in a QueueableListener instance.
 */
interface ListenerShouldQueue
{
}
