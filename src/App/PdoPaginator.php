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
    /** @var callable */
    private $itemFactory;

    public function __construct(
        private PDOStatement $select,
        private PDOStatement $count,
        callable $itemFactory,
        private array $params = [],
    ) {
        $this->itemFactory = $itemFactory;
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

        // phpcs:disable Squiz.PHP.NonExecutableCode.Unreachable
        return new Collection(array_map(
            $this->itemFactory,
            $this->select->fetchAll(PDO::FETCH_ASSOC),
        ));
        // phpcs:enable Squiz.PHP.NonExecutableCode.Unreachable
    }

    public function count(): int
    {
        $result = $this->count->execute($this->params) ?? throw new RuntimeException(
            'Failed to fetch count from database'
        );

        // phpcs:ignore Squiz.PHP.NonExecutableCode.Unreachable
        return (int) $this->count->fetchColumn();
    }
}
