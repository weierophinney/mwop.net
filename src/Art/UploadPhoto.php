<?php

declare(strict_types=1);

namespace Mwop\Art;

use DateTimeImmutable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;
use Throwable;

use function count;
use function in_array;
use function sprintf;

class UploadPhoto
{
    private const ALLOWED_MEDIA_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function __construct(
        private LoggerInterface $logger,
        private PhotoStorage $storage,
        private PhotoMapper $mapper,
        private Webhook\DatabaseBackup $backup,
    ) {
    }

    public function process(ServerRequestInterface $request): UploadPhotoResult
    {
        $form  = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        if (0 === count($files) || ! isset($files['imageUpload'])) {
            return UploadPhotoResult::fromError('Missing image file! Please resubmit with an image to upload.');
        }

        /** @var UploadedFileInterface $upload */
        $upload = $files['imageUpload'];
        if (! in_array($upload->getClientMediaType(), self::ALLOWED_MEDIA_TYPES, true)) {
            return UploadPhotoResult::fromError('Invalid image file! Must be a JPEG, PNG, or WEBP image.');
        }

        $sourceFile = $upload->getClientFilename();

        if (empty($form['description'])) {
            return UploadPhotoResult::fromError('Missing description! Please resubmit with a non-empty description.');
        }

        try {
            $filename = $this->storage->fromUploadedFile($upload);
        } catch (Throwable $e) {
            return $this->generateErrorResult('Error uploading image; please try again.', $e);
        }

        try {
            $this->storage->createThumbnail($filename);
        } catch (Throwable $e) {
            return $this->generateErrorResult('Error creating thumbnail from image; please try again.', $e);
        }

        $photo = new Photo(
            filename: $filename,
            description: $form['description'],
            createdAt: new DateTimeImmutable(),
            url: 'https://mwop.net/',
            sourceUrl: $sourceFile,
        );

        try {
            $this->mapper->create($photo);
        } catch (Throwable $e) {
            return $this->generateErrorResult('Error creating new record in database.', $e);
        }

        try {
            $this->backup->backup();
        } catch (Throwable $e) {
            return $this->generateErrorResult('Record created, but unable to backup database.', $e);
        }

        return UploadPhotoResult::withFilename($filename);
    }

    private function generateErrorResult(string $error, Throwable $e): UploadPhotoResult
    {
        $this->logger->error(sprintf(
            "[%s] %s: %s\nTrace:\n%s",
            self::class . $error,
            $e->getMessage(),
            $this->generateStackTrace($e)
        ));

        return UploadPhotoResult::fromError($error);
    }

    private function generateStackTrace(Throwable $e): string
    {
        $trace = '';
        do {
            $trace .= $e->getTraceAsString() . "\n";
        } while ($e = $e->getPrevious());

        return $trace;
    }
}
