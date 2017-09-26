<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Console;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use ZF\Console\Route;

class CreateAssetSymlinks
{
    const ASSET_MAP = [
        '../../node_modules/bootstrap/dist/js/bootstrap.js' => 'public/js/bootstrap.js',
        '../../node_modules/jquery/dist/jquery.js'          => 'public/js/jquery.js',
        '../../node_modules/lunr/lunr.js'                   => 'public/js/lunr.js',
    ];

    public function __invoke(Route $route, Console $console) : int
    {
        $console->writeLine('Creating asset symlinks... ', Color::BLUE);

        foreach (self::ASSET_MAP as $target => $link) {
            if (! file_exists($link)) {
                symlink($target, $link);
            }
        }

        $console->write('[DONE] ', Color::GREEN);
        $console->writeLine('Creating asset symlinks');

        return 0;
    }
}
