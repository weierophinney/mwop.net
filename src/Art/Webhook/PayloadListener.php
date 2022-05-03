<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

use ImagickException;
use JsonException;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToWriteFile;
use Mwop\App\HomePageCacheExpiration;
use Mwop\Art\Photo;
use Mwop\Art\PhotoMapper;
use Mwop\Art\PhotoStorage;
use PDOException;
use Psr\Log\LoggerInterface;

use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class PayloadListener
{
    public function __construct(
        private PhotoStorage $photos,
        private PhotoMapper $mapper,
        private DatabaseBackup $backup,
        private LoggerInterface $logger,
        private ErrorNotifier $notifier,
        private HomePageCacheExpiration $expireHomePageCache,
    ) {
    }

    public function __invoke(Payload $payload): void
    {
        // Parse
        $photo = $this->parsePayloadJson($payload);
        if (null === $photo) {
            return;
        }

        // Upload
        $filename = $this->upload($photo);
        if (null === $filename) {
            return;
        }
        $photo->injectFilename($filename);

        // Create thumbnail
        $this->createThumbnail($filename, $photo);

        // Add to database
        if (! $this->insertIntoDatabase($photo)) {
            // Failed
            return;
        }

        ($this->expireHomePageCache)();

        // Backup database
        $this->backupDatabase();
    }

    private function parsePayloadJson(Payload $payload): ?Photo
    {
        try {
            $photoData = json_decode($payload->json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $message = sprintf(
                "Unable to parse Instagram webhook payload: %s\nPayload: %s",
                $e->getMessage(),
                $payload->json
            );
            $this->notifier->sendNotification($message);
            $this->logger->warning($message);

            return null;
        }

        $photo = Photo::fromArray($photoData);
        if (null === $photo) {
            $message = sprintf(
                'Invalid Instagram payload detected: %s',
                $payload->json
            );
            $this->notifier->sendNotification($message);
            $this->logger->warning($message);

            return null;
        }

        return $photo;
    }

    private function upload(Photo $photo): ?string
    {
        try {
            return $this->photos->upload($photo->sourceUrl);
        } catch (UnableToWriteFile | FilesystemException $e) {
            $message = sprintf(
                'Failed to upload Instagram photo (%s): %s',
                $photo->sourceUrl,
                $e->getMessage(),
            );
            $this->notifier->sendNotification($message);
            $this->logger->warning($message);
        }

        return null;
    }

    private function createThumbnail(string $filename, Photo $photo): void
    {
        try {
            $this->photos->createThumbnail($filename);
        } catch (ImagickException | UnableToWriteFile | FilesystemException $e) {
            $message = sprintf(
                'Failed to scale Instagram photo (%s:%s): %s',
                $photo->filename(),
                $photo->sourceUrl,
                $e->getMessage(),
            );
            $this->notifier->sendNotification($message);
            $this->logger->warning($message);
        }
    }

    private function insertIntoDatabase(Photo $photo): bool
    {
        try {
            $this->mapper->create($photo);
            return true;
        } catch (PDOException $e) {
            $message = sprintf(
                'Failed to create database record for (%s:%s): %s',
                $photo->filename(),
                $photo->sourceUrl,
                $e->getMessage(),
            );
            $this->notifier->sendNotification($message);
            $this->logger->warning($message);
        }

        return false;
    }

    private function backupDatabase(): bool
    {
        try {
            $this->backup->backup();
        } catch (UnableToCopyFile | FilesystemException $e) {
            $message = sprintf(
                'Unable to backup photo database: %s',
                $e->getMessage(),
            );
            $this->notifier->sendNotification($message);
            $this->logger->warning($message);
            return false;
        }

        return true;
    }
}
