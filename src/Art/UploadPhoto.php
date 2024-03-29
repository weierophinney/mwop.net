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
use function mime_content_type;
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
        private Form\UploadRuleSet $form,
    ) {
    }

    public function process(ServerRequestInterface $request): UploadPhotoResult
    {
        $data  = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        if (0 === count($files) || ! isset($files['imageUpload'])) {
            return UploadPhotoResult::fromError('Missing image file! Please resubmit with an image to upload.');
        }

        /** @var UploadedFileInterface $upload */
        $upload   = $files['imageUpload'];
        $mimeType = $this->getMediaType($upload);

        if (! in_array($mimeType, self::ALLOWED_MEDIA_TYPES, true)) {
            return UploadPhotoResult::fromError(sprintf(
                'Invalid image file! Must be a JPEG, PNG, or WEBP image; received "%s"',
                $mimeType
            ));
        }

        $sourceFile = $upload->getClientFilename();

        $formResult = $this->form->validate($data);

        if (! $formResult->isValid()) {
            return UploadPhotoResult::fromError($formResult->description->message());
        }

        try {
            $filename = $this->storage->fromUploadedFile($upload, $mimeType);
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
            description: $formResult->description->value(),
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
            self::class,
            $error,
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

    private function getMediaType(UploadedFileInterface $upload): string
    {
        $filename = $upload->getStream()->getMetadata('uri');
        return mime_content_type($filename) ?: '';
    }
}
