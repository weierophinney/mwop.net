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
        'templates/blog/scripts.phtml.dist'   => 'templates/blog/scripts.phtml',
        'templates/blog/styles.phtml.dist'    => 'templates/blog/styles.phtml',
        'templates/layout/scripts.phtml.dist' => 'templates/layout/scripts.phtml',
        'templates/layout/styles.phtml.dist'  => 'templates/layout/styles.phtml',
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
