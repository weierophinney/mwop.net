<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use Psr\Cache\CacheItemPoolInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use ZF\Console\Route;

class ClearCache
{
    private $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function __invoke(Route $route, Console $console) : int
    {
        $console->write('Removing cached blog entries...', Color::BLUE);

        return $this->cache->clear()
            ? $this->reportSuccess($console, 30, $console->getWidth())
            : $this->reportError(
                $console,
                30,
                $console->getWidth(),
                'Cache pool indicated unsuccessful clear operation'
            );
    }

    /**
     * Report an error
     *
     * @param Console $console
     * @param int $width
     * @param int $length
     * @param string $message
     * @return int
     */
    private function reportError(Console $console, int $width, int $length, string $message) : int
    {
        if (($length + 9) > $width) {
            $console->writeLine('');
            $length = 0;
        }
        $spaces = $width - $length - 9;
        $console->writeLine(str_repeat('.', $spaces) . '[ ERROR ]', Color::RED);
        $console->writeLine($message);
        return 1;
    }

    /**
     * Report success
     */
    private function reportSuccess(Console $console, int $width, int $length) : int
    {
        if (($length + 8) > $width) {
            $console->writeLine('');
            $length = 0;
        }
        $spaces = $width - $length - 8;
        $spaces = ($spaces > 0) ? $spaces : 0;
        $console->writeLine(str_repeat('.', $spaces) . '[ DONE ]', Color::GREEN);
        return 0;
    }
}
