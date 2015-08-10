<?php
namespace Mwop\Blog\Console;

use Mwop\Blog;
use PDO;
use Zend\Console\ColorInterface as Color;

class SeedBlogDatabase
{
    private $delete = 'DELETE FROM posts WHERE 1';

    private $indices = [
        'CREATE INDEX visible ON posts ( created, draft, public )',
        'CREATE INDEX visible_tags ON posts ( tags, created, draft, public )',
        'CREATE INDEX visible_author ON posts ( author, created, draft, public )',
    ];

    private $initial = 'INSERT INTO posts
        SELECT
            %s AS id,
            %s AS path,
            %d AS created,
            %d AS updated,
            %s AS title,
            %s AS author,
            %d AS draft,
            %d AS public,
            %s AS body,
            %s AS tags';

    private $item = 'UNION SELECT
        %s,
        %s,
        %d,
        %d,
        %s,
        %s,
        %d,
        %d,
        %s,
        %s';

    private $table ='CREATE TABLE "posts" (
            id VARCHAR(255) NOT NULL PRIMARY KEY,
            path VARCHAR(255) NOT NULL,
            created UNSIGNED INTEGER NOT NULL,
            updated UNSIGNED INTEGER NOT NULL,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            draft INT(1) NOT NULL,
            public INT(1) NOT NULL,
            body TEXT NOT NULL,
            tags VARCHAR(255)
        )';

    public function __invoke($route, $console)
    {
        $basePath = $route->getMatchedParam('path');
        $dbPath   = $route->getMatchedParam('dbPath');

        $message = 'Generating blog post database';
        $length  = strlen($message);
        $width   = $console->getWidth();
        $console->write($message, Color::BLUE);

        $pdo = $this->createDatabase($dbPath, $console);

        $path = realpath($basePath) . '/data/posts';
        $trim = strlen(realpath($basePath)) + 1;

        $statements = [];
        foreach (new Blog\PhpFileFilter($path) as $fileInfo) {
            $entry  = include $fileInfo->getPathname();

            if (! $entry instanceof Blog\EntryEntity) {
                continue;
            }

            $entry  = $entry->getArrayCopy();
            $author = $entry['author'];
            if ($author instanceof Blog\AuthorEntity) {
                $author = $author->getArrayCopy();
            }

            $template = empty($statements) ? $this->initial : $this->item;

            $statements[] = sprintf(
                $template,
                $pdo->quote($entry['id']),
                $pdo->quote(substr($fileInfo->getPathname(), $trim)),
                $entry['created'],
                $entry['updated'],
                $pdo->quote($entry['title']),
                $pdo->quote(is_string($author) ? $author : $author['id']),
                $entry['is_draft'] ? 1 : 0,
                $entry['is_public'] ? 1 : 0,
                $pdo->quote($entry['body']),
                $pdo->quote(sprintf('|%s|', implode('|', $entry['tags'])))
            );
        }

        $pdo->exec(implode("\n", $statements));

        return $this->reportSuccess($console, $width, $length);
    }

    private function createDatabase($path, $console)
    {
        if (file_exists($path)) {
            $path = realpath($path);
            unlink($path);
        }

        if ($path[0] !== '/') {
            $path = realpath(getcwd()) . '/' . $path;
        }

        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
        $pdo->exec($this->table);
        foreach ($this->indices as $index) {
            $pdo->exec($index);
        }
        $pdo->commit();

        return $pdo;
    }

    /**
     * Report success
     *
     * @param \Zend\Console\Adapter\AdapterInterface $console
     * @param int $width
     * @param int $length
     * @return int
     */
    private function reportSuccess($console, $width, $length)
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
