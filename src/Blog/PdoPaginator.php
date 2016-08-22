<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use PDO;
use PDOStatement;
use Zend\Paginator\Adapter\AdapterInterface;

class PdoPaginator implements AdapterInterface
{
    protected $count;
    protected $params;
    protected $select;

    public function __construct(PdoStatement $select, PdoStatement $count, array $params = [])
    {
        $this->select = $select;
        $this->count  = $count;
        $this->params = $params;
    }

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

        return $this->select->fetchAll(PDO::FETCH_ASSOC);
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
