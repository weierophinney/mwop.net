<?php

declare(strict_types=1);

namespace Mwop\Art;

use Imagick;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Ramsey\Uuid\Uuid;

use function fopen;
use function parse_url;
use function pathinfo;
use function sprintf;

use const PATHINFO_EXTENSION;
use const PHP_URL_PATH;

class PhotoStorage
{
    public const TYPE_IMAGE     = 'images';
    public const TYPE_THUMBNAIL = 'thumbs';

    private MountManager $filesystem;

    public function __construct(
        Filesystem $images,
        Filesystem $thumbnails,
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
}
