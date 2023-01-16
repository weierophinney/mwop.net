<?php

declare(strict_types=1);

namespace Mwop\Art;

use Mezzio\Helper\UrlHelper;
use Mwop\Mastodon\ApiClient;
use Mwop\Mastodon\Credentials;
use Mwop\Mastodon\MediaFactory;
use Mwop\Mastodon\Status;
use RuntimeException;
use Webmozart\Assert\Assert;

final class PostToMastodon
{
    private const TEMPLATE = <<< TXT
        Photo: %s
        
        %s%s
        TXT;

    public function __construct(
        private readonly ApiClient $mastodon,
        private readonly Credentials $credentials,
        private readonly PhotoMapper $repo,
        private readonly MediaFactory $mediaFactory,
        private readonly UrlHelper $urlHelper,
        private string $baseUrl = 'https://mwop.net',
    ) {
    }

    public function photo(string $imageName): void
    {
        $this->createStatusFromPhoto($this->repo->fetch($imageName));
    }

    public function latest(): void
    {
        $photos = $this->repo->fetchAll();
        $photo  = null;
        foreach ($photos as $photo) {
            break;
        }

        if (! $photo instanceof Photo) {
            throw new RuntimeException('Unable to find any photos; cannot post latest');
        }

        $this->createStatusFromPhoto($photo);
    }

    private function createStatusFromPhoto(Photo $photo): void
    {
        $media     = ($this->mediaFactory)($photo->filename(), PhotoStorage::TYPE_IMAGE);
        $thumbnail = ($this->mediaFactory)($photo->filename(), PhotoStorage::TYPE_THUMBNAIL);

        $auth   = $this->mastodon->authenticate($this->credentials);
        $result = $this->mastodon->uploadMedia($auth, $media, $photo->description, $thumbnail);
        if (! $result->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Failed to upload media to Mastodon, with status code %d: %s',
                $result->getResponseObject()?->getStatusCode(),
                (string) $result->getResponseObject()?->getBody(),
            ));
        }

        $uploaded = json_decode($result->getResponseObject()->getBody(), true, flags: JSON_THROW_ON_ERROR);
        Assert::isMap($uploaded, 'Received unexpected result from Mastodon media upload');
        Assert::keyExists($uploaded, 'id', 'Mastodon media upload failed to return an ID');
        Assert::stringNotEmpty($uploaded['id'], 'Mastodon media upload returned an invalid ID');

        $result = $this->mastodon->createStatus(
            $auth,
            new Status(
                sprintf(
                    self::TEMPLATE,
                    $photo->description,
                    $this->baseUrl,
                    $this->urlHelper->generate('art.photo', ['image' => $photo->filename()])
                ),
                mediaIds: [$uploaded['id']],
            )
        );

        if (! $result->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Failed to create Mastodon status, with status code %d: %s',
                $result->getResponseObject()?->getStatusCode(),
                (string) $result->getResponseObject()?->getBody(),
            ));
        }
    }
}
