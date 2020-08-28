<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Console;

use Exception;
use Instagram\Api;
use Psr\Cache\CacheItemPoolInterface;

use function error_log;
use function sprintf;

class InstagramClient
{
    /** @var CacheItemPoolInterface */
    private $cachePool;

    /** @var bool */
    private $debug;

    /** @var string */
    private $login;

    /** @var string */
    private $password;

    /** @var string */
    private $profile;

    public function __construct(
        string $login,
        string $password,
        string $profile,
        CacheItemPoolInterface $cachePool,
        bool $debug = false
    ) {
        $this->login     = $login;
        $this->password  = $password;
        $this->profile   = $profile;
        $this->cachePool = $cachePool;
        $this->debug     = $debug;
    }

    /**
     * @return array Array<string, string>
     */
    public function fetchFeed(): array
    {
        $api = new Api($this->cachePool);
        try {
            $api->login($this->login, $this->password);
        } catch (Exception $e) {
            if ($this->debug) {
                error_log(sprintf('[Instagram] failed to login: %s', $e->getMessage()));
            }
            throw $e;
        }

        $profile = $api->getProfile($this->profile);
        $feed    = [];

        foreach ($profile->getMedias() as $media) {
            $feed[] = [
                'post_url'  => $media->getLink(),
                'image_url' => $media->getThumbnailSrc(),
            ];
        }

        return $feed;
    }
}
