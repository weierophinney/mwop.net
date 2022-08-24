<?php

declare(strict_types=1);

namespace Mwop\Art;

use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Laminas\Paginator\Paginator;
use Mwop\App\PdoPaginator;
use PDO;

use function array_map;
use function array_shift;
use function count;

class PdoPhotoMapper implements PhotoMapper
{
    public function __construct(
        private PDOConnection $pdo,
    ) {
    }

    public function fetchAll(): Paginator
    {
        $select = <<<'SQL'
            SELECT *
            FROM photos
            ORDER BY created DESC
            LIMIT :offset, :limit
            SQL;

        $count = 'SELECT COUNT(filename) FROM photos';

        $factory = function (array $row): Photo {
            return new Photo(
                url: $row['source'],
                sourceUrl: $row['source_url'],
                description: $row['description'],
                createdAt: new DateTimeImmutable($row['created']),
                filename: $row['filename'],
            );
        };

        $pdo = $this->pdo->connect();

        return new Paginator(new PdoPaginator(
            $pdo->prepare($select),
            $pdo->prepare($count),
            $factory,
        ));
    }

    public function fetch(string $filename): ?Photo
    {
        $select = <<<'SQL'
            SELECT * FROM photos WHERE filename = :filename
            SQL;

        $pdo       = $this->pdo->connect();
        $statement = $pdo->prepare($select);
        $statement->execute(['filename' => $filename]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $this->pdo->disconnect();

        if (0 === count($rows)) {
            return null;
        }

        $row = array_shift($rows);

        return new Photo(
            url: $row['source'],
            sourceUrl: $row['source_url'],
            description: $row['description'],
            createdAt: new DateTimeImmutable($row['created']),
            filename: $row['filename'],
        );
    }

    public function search(string $toMatch): Collection
    {
        $select = <<<'SQL'
            SELECT
                filename, description
            FROM search
            WHERE search match :query
            SQL;

        $pdo       = $this->pdo->connect();
        $statement = $pdo->prepare($select);
        if (! $statement->execute([':query' => $toMatch])) {
            $this->pdo->disconnect();
            return null;
        }

        return new Collection(array_map(
            fn (array $row): PhotoSearchResult => new PhotoSearchResult($row['filename'], $row['description']),
            $statement->fetchAll(PDO::FETCH_ASSOC)
        ));
    }

    public function create(Photo $photo): void
    {
        $insert = <<<'SQL'
            INSERT INTO photos (
                filename,
                source,
                source_url,
                description,
                created
            ) VALUES (
                :filename,
                :source,
                :source_url,
                :description,
                :created
            )
            SQL;

        $pdo       = $this->pdo->connect();
        $statement = $pdo->prepare($insert);

        $statement->execute([
            'filename'    => $photo->filename(),
            'source'      => $photo->url,
            'source_url'  => $photo->sourceUrl,
            'description' => $photo->description,
            'created'     => $photo->createdAt->format(DateTimeInterface::ISO8601),
        ]);

        $statement = null;
        $this->pdo->disconnect();
    }
}
