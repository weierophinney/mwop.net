<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Console;

use InstagramAPI\Instagram;
use InstagramAPI\Response\Model\CarouselMedia;
use InstagramAPI\Response\Model\Image_Versions2;
use InstagramAPI\Response\Model\Item;

class InstagramClient
{
    /** @var bool */
    private $debug;

    /** @var string */
    private $password;

    /** @var bool */
    private $truncatedDebug;

    /** @var string */
    private $username;

    public function __construct(string $username, string $password, bool $debug, bool $truncatedDebug)
    {
        $this->username       = $username;
        $this->password       = $password;
        $this->debug          = $debug;
        $this->truncatedDebug = $truncatedDebug;
    }

    /**
     * @return array<string, string>
     */
    public function fetchFeed() : array
    {
        $feed      = [];
        $instagram = new Instagram($this->debug, $this->truncatedDebug);

        $instagram->login($this->username, $this->password);

        // Get UserPK ID for me
        $userId = $instagram->people->getUserIdForName($this->username);

        // Get feed for user
        $response = $instagram->timeline->getUserFeed($userId, null);

        foreach ($response->getItems() as $item) {
            $image = null;

            foreach ($this->getImagesFromItem($item) as $candidate) {
                if ($image === null) {
                    $image = $candidate;
                    continue;
                }
                if ($image->getWidth() > $candidate->getWidth()) {
                    $image = $candidate;
                    continue;
                }
            }

            if ($image !== null) {
                $feed[] = [
                    'image_url' => $image->getUrl(),
                    'post_url'  => sprintf('https://instagram.com/p/%s', $item->getCode()),
                ];
            }
        }

        return $feed;
    }

    private function getImagesFromItem(Item $item) : iterable
    {
        $images = $item->getImageVersions2();
        if ($images instanceof Image_Versions2) {
            yield from $images->getCandidates();
        }

        $carousel = $item->getCarouselMedia();
        if ($carousel instanceof CarouselMedia) {
            foreach ($carousel as $carouselItem) {
                $images = $carouselItem->getImageVersions2();
                if (! $images instanceof Image_Versions2) {
                    continue;
                }
                yield from $images->getCandidates();
            }
        }

        yield from [];
    }
}
