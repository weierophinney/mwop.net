<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact,Squiz.PHP.NonExecutableCode.Unreachable

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog\Mapper;

use Closure;
use Laminas\Paginator\Adapter\AdapterInterface;
use Mwop\Blog\BlogPost;
use Mwop\Blog\CreateBlogPostFromDataArrayTrait;
use PDO;
use PDOStatement;
use RuntimeException;

use function array_map;
use function array_merge;

class PdoPaginator implements AdapterInterface
{
    use CreateBlogPostFromDataArrayTrait;

    public function __construct(
        protected PDOStatement $select,
        protected PDOStatement $count,
        protected array $params = [],
    ) {
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     * @return BlogPost[]
     */
    public function getItems($offset, $itemCountPerPage): array
    {
        $params = array_merge($this->params, [
            ':offset' => $offset,
            ':limit'  => $itemCountPerPage,
        ]);

        $result = $this->select->execute($params) ?? throw new RuntimeException('Failed to fetch items from database');

        return array_map(
            Closure::fromCallable([$this, 'createBlogPostFromDataArray']),
            $this->select->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function count(): int
    {
        $result = $this->count->execute($this->params) ?? throw new RuntimeException(
            'Failed to fetch count from database'
        );
        return (int) $this->count->fetchColumn();
    }
}
