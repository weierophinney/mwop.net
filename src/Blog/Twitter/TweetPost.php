<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Mezzio\Helper\UrlHelper;
use Mwop\Blog\BlogPost;
use Mwop\Blog\Mapper\MapperInterface;
use RuntimeException;
use Throwable;

use function sprintf;

class TweetPost
{
    use TweetTrait;

    private const TEMPLATE = <<<'END'
        From the archives: %title%

        %link%

        %tags%
        END;

    public function __construct(
        private MapperInterface $blogPostMapper,
        private TwitterFactory $factory,
        private UrlHelper $urlHelper,
        private string $logoPath
    ) {
    }

    public function __invoke(string $postIdentifier): void
    {
        $twitter = ($this->factory)();

        $twitter->post('statuses/update', [
            'status'    => $this->generateStatusFromPost(
                $this->getPost($postIdentifier),
                self::TEMPLATE,
            ),
            'media_ids' => [$this->generateMediaIDFromLogo($twitter)],
        ]);
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
