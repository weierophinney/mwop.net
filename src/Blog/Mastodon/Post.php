<?php

declare(strict_types=1);

namespace Mwop\Blog\Mastodon;

use Mezzio\Helper\UrlHelper;
use Mwop\Blog\BlogPost;
use Mwop\Blog\Mapper\MapperInterface;
use Mwop\Mastodon\ApiClient;
use Mwop\Mastodon\Credentials;
use Mwop\Mastodon\Status;
use RuntimeException;
use Throwable;

use function sprintf;

class Post
{
    use PostTrait;

    private const TEMPLATE = <<<'END'
        From the archives: %title%

        %link%

        %tags%
        END;

    public function __construct(
        private ApiClient $mastodon,
        private Credentials $credentials,
        private MapperInterface $blogPostMapper,
        private UrlHelper $urlHelper,
    ) {
    }

    public function __invoke(string $postIdentifier): void
    {
        $auth = $this->mastodon->authenticate($this->credentials);

        $status = new Status($this->generateStatusFromPost(
            $this->getPost($postIdentifier),
            self::TEMPLATE,
        ));

        $result = $this->mastodon->createStatus($auth, $status);

        if (! $result->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Posting to Mastodon failed with status %d',
                $result->response->getStatusCode(),
            ));
        }
    }

    private function getPost(string $postIdentifier): BlogPost
    {
        try {
            return $this->blogPostMapper->fetch($postIdentifier);
        } catch (Throwable $e) {
            throw new RuntimeException(sprintf(
                'Failed to retrieve post "%s": %s',
                $postIdentifier,
                $e->getMessage()
            ), previous: $e);
        }
    }
}
