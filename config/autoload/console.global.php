<?php

declare(strict_types=1);

use Mwop\Art\Console\FetchPhotoDatabase;
use Mwop\Blog\Console\FeedGenerator;
use Mwop\Blog\Console\GenerateSearchData;
use Mwop\Blog\Console\PostLatestToMastodon;
use Mwop\Blog\Console\PostToMastodon;
use Mwop\Blog\Console\SeedBlogDatabase;
use Mwop\Blog\Console\TagCloud;
use Mwop\Console\ClearResponseCache;
use Mwop\Console\ClearStaticCache;
use Mwop\Github\Console\Fetch;

return [
    'laminas-cli' => [
        'commands' => [
            'blog:feed-generator'       => FeedGenerator::class,
            'blog:generate-search-data' => GenerateSearchData::class,
            'blog:seed-db'              => SeedBlogDatabase::class,
            'blog:tag-cloud'            => TagCloud::class,
            'blog:mastodon:latest'      => PostLatestToMastodon::class,
            'blog:mastodon:post'        => PostToMastodon::class,
            'cache:clear-response'      => ClearResponseCache::class,
            'cache:clear-static'        => ClearStaticCache::class,
            'github:fetch-activity'     => Fetch::class,
            'photo:fetch-db'            => FetchPhotoDatabase::class,
        ],
    ],
];
