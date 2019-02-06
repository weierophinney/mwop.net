<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Github;

use Phly\Expressive\ConfigFactory;

class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'github'       => $this->getConfig(),
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getConfig() : array
    {
        return [
            'user'  => '',
            'limit' => 10,
        ];
    }

    public function getDependencies() : array
    {
        return [
            'factories' => [
                AtomReader::class    => AtomReaderFactory::class,
                'config-github'      => ConfigFactory::class,
                Console\Fetch::class => Console\FetchFactory::class,
            ],
        ];
    }
}
