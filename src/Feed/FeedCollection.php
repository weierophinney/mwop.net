<?php

declare(strict_types=1);

namespace Mwop\Feed;

use Illuminate\Support\Collection;

class FeedCollection extends Collection
{
    /**
     * Filter items through a filter chain.
     *
     * Passes each item through a collection of filters, keeping only
     * those items that pass all filters.
     *
     * @return static
     */
    public function filterChain(array $filters): self
    {
        $filters = Collection::make($filters);
        return $this->filter(function (mixed $item) use ($filters): bool {
            return $filters
                ->reduce(
                    fn ($keep, callable $filter) => ! $keep ? $keep : $filter($item),
                    true
                );
        });
    }
}
