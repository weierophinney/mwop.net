<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Mezzio\Helper\UrlHelper;
use Mwop\Blog\BlogPost;
use Mwop\Blog\Mapper\MapperInterface;
use RuntimeException;

use function sprintf;

class TweetLatest
{
    use TweetTrait;

    private const TEMPLATE = <<<'END'
        Blogged: %title%

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

    public function __invoke(): void
    {
        $twitter = ($this->factory)();

        $twitter->post('statuses/update', [
            'status' => $this->generateStatusFromPost(
                $this->getFirstPost(),
                self::TEMPLATE,
            ),
            // 'media_ids' => [$this->generateMediaIDFromLogo($twitter)],
        ]);
    }

    private function getFirstPost(): BlogPost
    {
        foreach ($this->blogPostMapper->fetchAll() as $post) {
            return $post;
        }

        throw new RuntimeException(sprintf(
            'Failed to retrieve the first blog post; cannot tweet a link to it.'
        ));
    }
}
