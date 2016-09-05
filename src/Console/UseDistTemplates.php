<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Console;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use ZF\Console\Route;

class UseDistTemplates
{
    private $distFileMap = [
        'templates/blog/scripts.mustache.dist'   => 'templates/blog/scripts.mustache',
        'templates/blog/styles.mustache.dist'    => 'templates/blog/styles.mustache',
        'templates/layout/scripts.mustache.dist' => 'templates/layout/scripts.mustache',
        'templates/layout/styles.mustache.dist'  => 'templates/layout/styles.mustache',
    ];

    public function __invoke(Route $route, Console $console) : int
    {
        $path = $route->getMatchedParam('path');

        $console->writeLine('Enabling dist templates... ', Color::BLUE);

        foreach ($this->distFileMap as $source => $target) {
            $source = sprintf('%s/%s', $path, $source);
            $target = sprintf('%s/%s', $path, $target);
            copy($source, $target);
        }

        $console->write('[DONE] ', Color::GREEN);
        $console->writeLine('Enabling dist templates');

        return 0;
    }
}
