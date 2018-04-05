<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use DateTime;
use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use Mni\FrontYAML\Parser;
use Mwop\Blog\MarkdownFileFilter;
use PDO;
use Symfony\Component\Yaml\Parser as YamlParser;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use ZF\Console\Route;

class SeedBlogDatabase
{
    private $authors = [];

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

    /**
     * Delimiter between post summary and extended body
     *
     * @var string
     */
    private $postDelimiter = '<!--- EXTENDED -->';

    private $searchTable = 'CREATE VIRTUAL TABLE search USING FTS4(
            id,
            created,
            title,
            body,
            tags
        )';

    private $searchTrigger = 'CREATE TRIGGER after_posts_insert
            AFTER INSERT ON posts
            BEGIN
                INSERT INTO search (
                    id,
                    created,
                    title,
                    body,
                    tags
                )
                VALUES (
                    new.id,
                    new.created,
                    new.title,
                    new.body,
                    new.tags
                );
            END
        ';

    private $table = 'CREATE TABLE "posts" (
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

    public function __invoke(Route $route, Console $console) : int
    {
        $basePath    = $route->getMatchedParam('path');
        $postsPath   = $route->getMatchedParam('postsPath');
        $authorsPath = $route->getMatchedParam('authorsPath');
        $dbPath      = $route->getMatchedParam('dbPath');

        $message = 'Generating blog post database';
        $length  = strlen($message);
        $width   = $console->getWidth();
        $console->write($message, Color::BLUE);

        $pdo = $this->createDatabase($dbPath, $console);

        $path = sprintf('%s/%s', realpath($basePath), ltrim($postsPath));
        $trim = strlen(realpath($basePath)) + 1;

        $parser     = new Parser(null, new CommonMarkParser());
        $statements = [];
        foreach (new MarkdownFileFilter($path) as $fileInfo) {
            $path     = $fileInfo->getPathname();
            $document = $parser->parse(file_get_contents($path));
            $metadata = $document->getYAML();
            $html     = $document->getContent();
            $parts    = explode($this->postDelimiter, $html, 2);
            $body     = $parts[0];
            $extended = isset($parts[1]) ? $parts[1] : '';
            $author   = $this->getAuthor($metadata['author'], $authorsPath);
            $template = empty($statements) ? $this->initial : $this->item;

            $statements[] = sprintf(
                $template,
                $pdo->quote($metadata['id']),
                $pdo->quote(substr($path, $trim)),
                (new DateTime($metadata['created']))->getTimestamp(),
                (new DateTime($metadata['updated']))->getTimestamp(),
                $pdo->quote($metadata['title']),
                $pdo->quote($author['id']),
                $metadata['draft'] ? 1 : 0,
                $metadata['public'] ? 1 : 0,
                $pdo->quote($body),
                $pdo->quote(sprintf('|%s|', implode('|', $metadata['tags'])))
            );
        }

        $pdo->exec(implode("\n", $statements));

        return $this->reportSuccess($console, $width, $length);
    }

    private function createDatabase(string $path, Console $console) : PDO
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
        $pdo->exec($this->searchTable);
        $pdo->exec($this->searchTrigger);
        $pdo->commit();

        return $pdo;
    }

    /**
     * Retrieve author metadata.
     *
     * @param string $author
     * @param string $authorsPath
     * @return string[]
     */
    private function getAuthor(string $author, string $authorsPath) : array
    {
        if (isset($this->authors[$author])) {
            return $this->authors[$author];
        }

        $path = sprintf('%s/%s.yml', $authorsPath, $author);
        if (! file_exists($path)) {
            $this->authors[$author] = ['id' => $author, 'name' => $author, 'email' => '', 'uri' => ''];
            return $this->authors[$author];
        }

        $this->authors[$author] = (new YamlParser())->parse(file_get_contents($path));
        return $this->authors[$author];
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
