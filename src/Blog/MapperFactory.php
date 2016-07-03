<?php
namespace Mwop\Blog;

use PDO;

class MapperFactory
{
    public function __invoke($container)
    {
        $config = $container->get('config');
        $config = $config['blog'];
        $pdo = new PDO($config['db']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return new PdoMapper($pdo);
    }
}
