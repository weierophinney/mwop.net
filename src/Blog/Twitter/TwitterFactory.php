<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterFactory
{
    public function __construct(
        private string $consumerKey,
        private string $consumerSecret,
        private string $accessToken,
        private string $accessTokenSecret,
    ) {
    }

    public function __invoke(): TwitterOAuth
    {
        return new TwitterOAuth(
            $this->consumerKey,
            $this->consumerSecret,
            $this->accessToken,
            $this->accessTokenSecret
        );
    }
}
