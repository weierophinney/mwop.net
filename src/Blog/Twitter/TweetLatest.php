<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Mwop\App\Factory\PlatesFunctionsDelegator;
use Mwop\Blog\BlogPost;
use Mwop\Blog\Mapper\MapperInterface;
use RuntimeException;

class TweetLatest
{
    private const TEMPLATE = <<<'END'
        Blogged: %title%

        %link%

        %tags%
        END;

    public function __construct(
        private MapperInterface $blogPostMapper,
        private TwitterFactory $factory,
        private PlatesFunctionsDelegator $urlHelper,
        private string $logoPath
    ) {
    }

    public function __invoke(): void
    {
        $twitter = ($this->factory)();

        $twitter->post('statuses/update', [
            'status' => $this->generateStatusFromFirstPost($this->getFirstPost()),
            'media_ids' => [$this->generateMediaIDFromLogo($twitter)],
        ]);
    }

    private function generateStatusFromFirstPost(BlogPost $post): string
    {
        return str_replace(
            [
                '%title%',
                '%link%',
                '%tags%',
            ],
            [
                $post->title,
                $this->urlHelper->postUrl($post),
                array_map(fn (string $tag) => sprintf('#%s', $tag), $post->tags)
            ],
            self::TEMPLATE,
        );
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

    private function generateMediaIDFromLogo(TwitterOAuth $twitter): string
    {
        $media = $twitter->upload('media/upload', ['media' => $this->logoPath]);
        return $media->media_id_string;
    }
}
