<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Mapper;

use Closure;
use Mwop\Blog\CreateBlogPostFromDataArray;
use PDO;
use PDOStatement;
use RuntimeException;
use Zend\Paginator\Adapter\AdapterInterface;

class PdoPaginator implements AdapterInterface
{
    use CreateBlogPostFromDataArray;

    /** @var int */
    protected $count;
    
    /** @var array<string, mixed> */
    protected $params;

    /** @var PDOStatement */
    protected $select;

    public function __construct(PDOStatement $select, PDOStatement $count, array $params = [])
    {
        $this->select = $select;
        $this->count  = $count;
        $this->params = $params;
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     * @return \Mwop\Blog\BlogPost[]
     */
    public function getItems($offset, $itemCountPerPage) : array
    {
        $params = array_merge($this->params, [
            ':offset' => $offset,
            ':limit'  => $itemCountPerPage,
        ]);

        $result = $this->select->execute($params);

        if (! $result) {
            throw new RuntimeException('Failed to fetch items from database');
        }

        return array_map(
            Closure::fromCallable([$this, 'createBlogPostFromDataArray']),
            $this->select->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function count() : int
    {
        $result = $this->count->execute($this->params);
        if (! $result) {
            throw new RuntimeException('Failed to fetch count from database');
        }
        return $this->count->fetchColumn();
    }
}
