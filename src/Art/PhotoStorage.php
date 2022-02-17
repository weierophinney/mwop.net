<?php

declare(strict_types=1);

namespace Mwop\Art;

use Imagick;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use RuntimeException;

use function fopen;
use function in_array;
use function parse_url;
use function pathinfo;
use function sprintf;

use const PATHINFO_EXTENSION;
use const PHP_URL_PATH;

class PhotoStorage
{
    public const TYPE_IMAGE     = 'images';
    public const TYPE_THUMBNAIL = 'thumbs';

    private const TYPE_ALLOWED = [
        self::TYPE_IMAGE,
        self::TYPE_THUMBNAIL,
    ];

    private MountManager $filesystem;

    public function __construct(
        Filesystem $images,
        Filesystem $thumbnails,
        private ResponseFactoryInterface $responseFactory
    ) {
        $this->filesystem = new MountManager([
            'images' => $images,
            'thumbs' => $thumbnails,
        ]);
    }

    public function upload(string $sourceUrl): string
    {
        $suffix   = pathinfo(parse_url($sourceUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
        $filename = sprintf('%s.%s', Uuid::uuid6()->__toString(), $suffix);
        $source   = fopen($sourceUrl, 'r');

        $this->filesystem->writeStream(sprintf('images://%s', $filename), $source);

        return $filename;
    }

    public function createThumbnail(string $imageName): void
    {
        $image = new Imagick();
        $image->readImageBlob($this->filesystem->read('images://' . $imageName));
        $image->scaleImage(180, 0);
        $this->filesystem->write('thumbs://' . $imageName, $image->getImageBlob());
        unset($image);
    }

    /** @psalm-param PhotoStorage::TYPE_IMAGE|PhotoStorage::TYPE_THUMBNAIL $type */
    public function getPsr7ResponseForImageName(string $imageName, string $type): ResponseInterface
    {
        $this->guardType($type);

        $filename = $this->getFilenameForType($imageName, $type);

        if (! $this->filesystem->has($filename)) {
            return $this->responseFactory->createResponse(404);
        }

        $response = $this->responseFactory->createResponse(200)
            ->withHeader('Content-Type', $this->filesystem->mimeType($filename))
            ->withHeader('Content-Length', $this->filesystem->fileSize($filename));
        $response->getBody()->write($this->filesystem->read($filename));

        return $response;
    }

    private function guardType(string $type): void
    {
        if (! in_array($type, self::TYPE_ALLOWED, true)) {
            throw new RuntimeException(sprintf(
                '%1$s expects a $type argument of %1$s::TYPE_THUMBNAIL or %1$s::TYPE_IMAGE',
                $this::class,
            ));
        }
    }

    private function getFilenameForType(string $imageName, string $type): string
    {
        return sprintf('%s://%s', $type, $imageName);
    }
}
