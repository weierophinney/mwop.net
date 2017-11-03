<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Console;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use ZF\Console\Route;

class CopyAssetSymlinks
{
    public function __invoke(Route $route, Console $console) : int
    {
        $console->writeLine('Copying asset symlinks... ', Color::BLUE);

        foreach (CreateAssetSymlinks::ASSET_MAP as $origin => $target) {
            if (file_exists($target)) {
                unlink($target);
            }

            $origin = str_replace('../../', '', $origin);

            copy($origin, $target);
        }

        $console->write('[DONE] ', Color::GREEN);
        $console->writeLine('Copying asset symlinks');

        return 0;
    }
}
