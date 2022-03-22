<?php

declare(strict_types=1);

namespace Mwop\Art;

use PDO;

class PDOConnection
{
    private ?PDO $pdo = null;

    public function __construct(private string $dsn)
    {
    }

    public function connect(): PDO
    {
        if (null !== $this->pdo) {
            // Unset the connection to flush any writes to the system
            $this->pdo = null;
        }

        $this->pdo = new PDO(dsn: $this->dsn);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $this->pdo;
    }

    public function disconnect(): void
    {
        $this->pdo = null;
    }
}
