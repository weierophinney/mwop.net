<?php

declare(strict_types=1);

namespace Mwop\Blog\Flickr;

use function sprintf;

trait PhotoUrlTrait
{
    public function thumbnail(string $photoId, string $photoSecret, string $server): string
    {
        return $this->generatePhotoUrl($photoId, $photoSecret, $server);
    }

    public function original(string $photoId, string $photoSecret, string $server): string
    {
        return $this->generatePhotoUrl($photoId, $photoSecret, $server, 'o');
    }

    public static function web(string $photoId, string $userId): string
    {
        return sprintf(
            'https://www.flickr.com/photos/%s/%s/',
            $userId,
            $photoId,
        );
    }

    private function generatePhotoUrl(
        string $photoId,
        string $photoSecret,
        string $server,
        ?string $sizeString = null,
    ): string {
        if (null === $sizeString) {
            return sprintf(
                'https://live.staticflickr.com/%s/%s_%s.jpg',
                $server,
                $photoId,
                $photoSecret
            );
        }

        return sprintf(
            'https://live.staticflickr.com/%s/%s_%s_%s.jpg',
            $server,
            $photoId,
            $photoSecret,
            $sizeString,
        );
    }
}
