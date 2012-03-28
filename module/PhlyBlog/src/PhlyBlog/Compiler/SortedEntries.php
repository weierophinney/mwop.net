<?php
namespace PhlyBlog\Compiler;

use SplPriorityQueue,
    PhlyBlog\EntryEntity;

class SortedEntries extends SplPriorityQueue
{
    /**
     * Sorting on timestamps
     * 
     * @param  int $priority1 
     * @param  int $priority2 
     * @return int
     */
    public function compare($priority1, $priority2)
    {
        if ($priority1 > $priority2) {
            return 1;
        }
        if ($priority1 < $priority2) {
            return -1;
        }
        // equal
        return 0;
    }

    public function insert($data, $priority)
    {
        if (!$data instanceof EntryEntity) {
            throw new InvalidArgumentException(sprintf(
                '%s expects an EntryEntity; received %s',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }
        parent::insert($data, $priority);
    }
}
