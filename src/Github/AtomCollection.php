<?php

declare(strict_types=1);

namespace Mwop\Github;

use Illuminate\Support\Collection;

class AtomCollection extends Collection
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
        return $this->filter(
            fn (mixed $item): bool => $filters->reduce(
                fn (bool $keep, callable $filter): bool  => ! $keep ? $keep : $filter($item),
                true,
            )
        );
    }
}
