<?php
namespace Mwop\Blog;

use PDO;

class SeedBlogDatabaseFactory
{
    public function __invoke($services)
    {
        $config = $services->get('Config');
        $config = $config['blog'];
        $pdo = new PDO($config['db']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return new SeedBlogDatabase($pdo);
    }
}
