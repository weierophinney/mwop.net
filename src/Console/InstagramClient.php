<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact

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
    public function __construct(
        private string $login,
        private string $password,
        private string $profile,
        private CacheItemPoolInterface $cachePool,
        private bool $debug = false,
    ) {
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
