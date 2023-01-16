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

use function sprintf;

class PostLatest
{
    use PostTrait;

    private const TEMPLATE = <<<'END'
        Blogged: %title%

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

    public function __invoke(): void
    {
        $auth = $this->mastodon->authenticate($this->credentials);

        $status = new Status($this->generateStatusFromPost(
            $this->getFirstPost(),
            self::TEMPLATE,
        ));

        $result = $this->mastodon->createStatus($auth, $status);

        if (! $result->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Posting latest blog entry to Mastodon failed with status %d',
                $result->response->getStatusCode(),
            ));
        }
    }

    private function getFirstPost(): BlogPost
    {
        foreach ($this->blogPostMapper->fetchAll() as $post) {
            return $post;
        }

        throw new RuntimeException(sprintf(
            'Failed to retrieve the first blog post; cannot post a link to it to Mastodon.'
        ));
    }
}
