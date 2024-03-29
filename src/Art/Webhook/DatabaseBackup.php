<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

use League\Flysystem\FilesystemOperator;
use League\Flysystem\MountManager;

class DatabaseBackup
{
    private MountManager $filesystem;

    public function __construct(
        FilesystemOperator $application,
        FilesystemOperator $remote,
        private string $database,
    ) {
        $this->filesystem = new MountManager([
            'app'    => $application,
            'backup' => $remote,
        ]);
    }

    public function backup(): void
    {
        $this->filesystem->copy('app://' . $this->database, 'backup://' . $this->database);

        // If WAL pragma is enabled, copy that file
        if ($this->filesystem->has('app://' . $this->database . '-wal')) {
            $this->filesystem->copy(
                'app://' . $this->database . '-wal',
                'backup://' . $this->database . '-wal'
            );
        }
    }
}
