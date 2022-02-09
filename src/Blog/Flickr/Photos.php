<?php

declare(strict_types=1);

namespace Mwop\Blog\Flickr;

use Illuminate\Support\Collection;
use JeroenG\Flickr\Flickr;

use function array_map;

class Photos
{
    public function __construct(
        private Flickr $flickr,
    ) {
    }

    /**
     * Query the Flickr API for free-to-use photos
     *
     * text - free text search
     * license: see flickr.photos.licenses.getinfo
     * privacy_filter: 1 (public only)
     * safe_search: 1 (safe - not moderated or restricted)
     * content_type: 4 (photos and screenshots)
     * media: photos
     */
    public function search(string $search, int $page = 1, int $perPage = 5): Collection
    {
        $result = $this->flickr->request('flickr.photos.search', [
            'text'           => $search,
            'license'        => '1,2,3,4,5,6,7,9,10',
            'privacy_filter' => 1,
            'safe_search'    => 1,
            'content_type'   => 4,
            'media'          => 'photos',
            'per_page'       => $perPage,
            'page'           => $page,
        ]);

        return new Collection(array_map(
            fn (array $photo): PhotoSearchResult => PhotoSearchResult::fromArray($photo),
            $result->photos['photo']
        ));
    }

    public function fetchImage(string $id, string $secret): Photo
    {
        $result = $this->flickr->photoInfo($id, $secret);
        return Photo::fromArray($result->photo);
    }
}
