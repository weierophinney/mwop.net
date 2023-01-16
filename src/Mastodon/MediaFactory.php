<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Mwop\Art\PhotoStorage;
use Mwop\Art\Storage\PhotoRetrieval;

final class MediaFactory
{
    public function __construct(
        private PhotoRetrieval $repo,
    ) {
    }

    /** @psalm-param PhotoStorage::TYPE_IMAGE|PhotoStorage::TYPE_THUMBNAIL $type */
    public function __invoke(string $imageName, string $type): Media
    {
        $response = $this->repo->fetchAsResponse($imageName, $type);
        return new Media(
            $response->getBody(),
            $imageName,
            $response->getHeaderLine('Content-Type')
        );
    }
}
