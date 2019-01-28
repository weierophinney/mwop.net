<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Blog\Mapper;

use PDO;
use Psr\Container\ContainerInterface;

class MapperFactory
{
    public function __invoke(ContainerInterface $container) : PdoMapper
    {
        $config = $container->get('config')['blog']['db'] ?? [];
        $pdo    = new PDO($config);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return new PdoMapper($pdo);
    }
}
