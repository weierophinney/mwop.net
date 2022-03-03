<?php

declare(strict_types=1);

namespace Mwop\Now;

use Illuminate\Support\Collection;
use League\Flysystem\StorageAttributes;
use Psr\Container\ContainerInterface;

use function basename;
use function preg_match;

class ArchivesFactory
{
    public function __invoke(ContainerInterface $container): Collection
    {
        return (new Collection(
            $container->get(NowAndThenFilesystem::class)->listContents('.')
                ->filter(fn (StorageAttributes $item): bool => $item->isFile())
                ->filter(fn (StorageAttributes $file): bool => (bool) preg_match('/^\d{4}-\d{2}\.md$/', basename($file->path())))
                ->sortByPath()
                ->toArray()
        ))->reverse();
    }
}
