<?php

declare(strict_types=1);

namespace Mwop\Art\Storage;

use Aws\S3\S3Client;
use Mwop\Art\PhotoStorage;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

use function in_array;
use function sprintf;

class PhotoRetrieval
{
    private const TYPE_ALLOWED = [
        PhotoStorage::TYPE_IMAGE,
        PhotoStorage::TYPE_THUMBNAIL,
    ];

    private const TYPE_PREFIX_MAP = [
        PhotoStorage::TYPE_IMAGE     => '',
        PhotoStorage::TYPE_THUMBNAIL => 'thumbs/',
    ];

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private S3Client $client,
        private string $bucket,
        private string $pathPrefix = 'art/',
    ) {
    }

    /** @psalm-param PhotoStorage::TYPE_IMAGE|PhotoStorage::TYPE_THUMBNAIL $type */
    public function fetchAsResponse(string $imageName, string $type): ResponseInterface
    {
        $this->guardType($type);

        $result = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key'    => sprintf('%s%s%s', $this->pathPrefix, self::TYPE_PREFIX_MAP[$type], $imageName),
        ]);

        return $this->responseFactory->createResponse(200)
            ->withHeader('Content-Type', $result['ContentType'])
            ->withHeader('Content-Length', $result['ContentLength'])
            ->withBody($result['Body']);
    }

    private function guardType(string $type): void
    {
        if (! in_array($type, self::TYPE_ALLOWED, true)) {
            throw new RuntimeException(sprintf(
                '%1$s::fetch expects a $type argument of %2$s::TYPE_THUMBNAIL or %2$s::TYPE_IMAGE',
                $this::class,
                PhotoStorage::class,
            ));
        }
    }
}
