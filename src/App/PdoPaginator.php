<?php

declare(strict_types=1);

namespace Mwop\App;

use Illuminate\Support\Collection;
use Laminas\Paginator\Adapter\AdapterInterface;
use PDO;
use PDOStatement;
use RuntimeException;

use function array_map;
use function array_merge;

class PdoPaginator implements AdapterInterface
{
    public function __construct(
        private PDOStatement $select,
        private PDOStatement $count,
        private callable $itemFactory,
        private array $params = [],
    ) {
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     */
    public function getItems($offset, $itemCountPerPage): Collection
    {
        $params = array_merge($this->params, [
            ':offset' => $offset,
            ':limit'  => $itemCountPerPage,
        ]);

        $result = $this->select->execute($params) ?? throw new RuntimeException('Failed to fetch items from database');

        return new Collection(array_map(
            $this->itemFactory,
            $this->select->fetchAll(PDO::FETCH_ASSOC),
        ));
    }

    public function count(): int
    {
        $result = $this->count->execute($this->params) ?? throw new RuntimeException(
            'Failed to fetch count from database'
        );
        return (int) $this->count->fetchColumn();
    }
}
