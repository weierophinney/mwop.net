<?php
namespace Mwop\Blog;

use PDO;
use Zend\Paginator\Paginator;
use Zend\Tag\Cloud;

class PdoMapper implements MapperInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function fetch($id)
    {
        $select = $this->pdo->prepare('SELECT * from posts WHERE id = :id');
        if (! $select->execute([':id' => $id])) {
            return false;
        }

        return $select->fetch();
    }

    public function fetchAll()
    {
        $select = 'SELECT * FROM posts WHERE draft = 0 AND public = 1 ORDER BY created DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM posts WHERE draft = 0 AND public = 1';
        return $this->preparePaginator($select, $count);
    }

    public function fetchAllByAuthor($author)
    {
        $select = 'SELECT * FROM posts WHERE draft = 0 AND public = 1 AND author = :author ORDER BY created DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM posts WHERE draft = 0 AND public = 1 AND author = :author';
        return $this->preparePaginator($select, $count, [':author' => $author]);
    }

    public function fetchAllByTag($tag)
    {
        $select = 'SELECT * FROM posts WHERE draft = 0 AND public = 1 AND tags LIKE :tag ORDER BY created DESC LIMIT :offset, :limit';
        $count  = 'SELECT COUNT(id) FROM posts WHERE draft = 0 AND public = 1 AND tags LIKE :tag';
        return $this->preparePaginator($select, $count, [':tag' => sprintf('%%|%s|%%', $tag)]);
    }

    public function fetchTagCloud($urlTemplate = '/blog/tag/%s', array $options = [])
    {
        $options['fontSizeUnit'] = isset($options['fontSizeUnit']) ? $options['fontSizeUnit'] : '%';
        $options['minFontSize'] = isset($options['minFontSize']) ? $options['minFontSize'] : 80;
        $options['maxFontSize'] = isset($options['maxFontSize']) ? $options['maxFontSize'] : 300;

        $select = $this->pdo->prepare('SELECT tags FROM posts WHERE tags IS NOT NULL AND tags != ""');
        $select->execute();

        $tagsByRow = array_map(function ($value) {
            return explode('|', trim($value, '|'));
        }, $select->fetchAll(PDO::FETCH_COLUMN));

        $options['tags'] = array_reduce($tagsByRow, function ($carry, $item) use ($urlTemplate) {
            foreach ($item as $tag) {
                if (! isset($carry[$tag])) {
                    $carry[$tag] = [
                        'title' => $tag,
                        'weight' => 0,
                        'params' => [
                            'url' => sprintf($urlTemplate, str_replace(' ', '+', $tag))
                        ],
                    ];
                }
                $carry[$tag]['weight'] += 1;
            }
            return $carry;
        }, []);

        return new Cloud($options);
    }

    private function preparePaginator($select, $count, array $params = [])
    {
        $select = $this->pdo->prepare($select);
        $count  = $this->pdo->prepare($count);
        return new Paginator(new PdoPaginator(
            $select,
            $count,
            $params
        ));
    }
}
