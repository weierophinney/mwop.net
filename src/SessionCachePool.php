<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Cache\Adapter\Predis\PredisCachePool;

/**
 * PSR-6 cache pool implementation for sessions.
 *
 * Empty extension to allow typehinting/`::class` resolution.
 */
class SessionCachePool extends PredisCachePool
{
}
