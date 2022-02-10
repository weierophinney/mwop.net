<?php

declare(strict_types=1);

namespace Mwop\Blog\Images;

use Illuminate\Support\Collection;
use Psr\Http\Message\RequestFactoryInterface;
use RuntimeException;

use function array_map;
use function implode;
use function json_decode;
use function sprintf;
use function urlencode;

use const JSON_THROW_ON_ERROR;

class Images
{
    private const SEARCH_URL = 'https://api.openverse.engineering/v1/images/';

    public function __construct(
        private ApiClient $http,
        private RequestFactoryInterface $requestFactory,
    ) {
    }

    /**
     * Query the OpenVerse image API for CC/PD photos
     *
     * @return Collection[Image]
     */
    public function search(string $search, int $page = 1, int $perPage = 5): Collection
    {
        $request  = $this->requestFactory->createRequest('GET', self::SEARCH_URL);
        $uri      = $request->getUri()->withQuery($this->createImageQuery($search, $perPage, $page));
        $request  = $request->withUri($uri);
        $response = $this->http->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException(sprintf(
                'Search was unsuccessful (%d): %s',
                $response->getStatusCode(),
                $response->getBody()->__toString(),
            ));
        }

        $data = json_decode($response->getBody()->__toString(), true, flags: JSON_THROW_ON_ERROR);
        if ($data['result_count'] < 1) {
            return new Collection();
        }

        return new Collection(array_map(
            fn (array $photo): Image => Image::fromArray($photo),
            $data['results'],
        ));
    }

    private function createImageQuery(string $searchTerms, int $perPage, int $page): string
    {
        $toQuery = [
            'q'            => urlencode($searchTerms),
            'page'         => $page,
            'page_size'    => $perPage,
            'license_type' => 'all-cc',
            'extension'    => 'png,jpg,jpeg,webp',
            'aspect_ratio' => 'wide,square',
            'size'         => 'medium,large',
        ];

        $pairs = [];

        foreach ($toQuery as $key => $value) {
            $pairs[] = sprintf('%s=%s', $key, $value);
        }

        return implode('&', $pairs);
    }
}
